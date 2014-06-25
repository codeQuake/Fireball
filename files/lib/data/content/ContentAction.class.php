<?php
namespace cms\data\content;

use cms\system\cache\builder\ContentCacheBuilder;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * Executes content-related actions.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentAction extends AbstractDatabaseObjectAction implements ISortableAction {
	protected $className = 'cms\data\content\ContentEditor';
	protected $resetCache = array('create', 'delete', 'toggle', 'update', 'updatePosition', 'restoreRevision');
	protected $permissionsDelete = array(
		'admin.cms.content.canAddContent'
	);

	protected $permissionsUpdate = array(
		'admin.cms.content.canAddContent'
	);
	protected $requireACP = array(
		'delete',
		'updatePosition'
	);

	public function validateUpdatePosition() {
		WCF::getSession()->checkPermissions(array(
			'admin.cms.content.canAddContent'
		));

		if (! isset($this->parameters['data']['structure']) || ! is_array($this->parameters['data']['structure'])) {
			throw new UserInputException('structure');
		}
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		foreach ($this->parameters['data']['structure'] as $parentID => $contentIDs) {
			if ($parentID) {
				if (! isset($contents[$parentID])) {
					throw new UserInputException('structure');
				}

				$this->objects[$parentID] = new ContentEditor($contents[$parentID]);
			}

			foreach ($contentIDs as $contentID) {
				if (! isset($contents[$contentID])) {
					throw new UserInputException('structure');
				}

				$this->objects[$contentID] = new ContentEditor($contents[$contentID]);
			}
		}
	}

	public function updatePosition() {
		WCF::getDB()->beginTransaction();
		foreach ($this->parameters['data']['structure'] as $parentID => $contentIDs) {
			$position = 1;
			foreach ($contentIDs as $contentID) {
				$this->objects[$contentID]->update(array(
					'parentID' => $parentID != 0 ? $this->objects[$parentID]->contentID : null,
					'showOrder' => $position ++
				));
			}
		}
		WCF::getDB()->commitTransaction();
		//create revision
		$this->parameters['action'] = 'updatePosition';
		$this->createRevision();
	}

	protected function createRevision() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		$action = 'create';
		if (isset($this->parameters['action'])) {
			$action = $this->parameters['action'];
		}

		foreach ($this->objects as $object) {
			call_user_func(array($this->className, 'createRevision'), array('contentID' => $object->getObjectID(), 'action' => $action, 'userID' => WCF::getUser()->userID, 'username' => WCF::getUser()->username, 'time' => TIME_NOW, 'data' => serialize($object->getDecoratedObject()->getData())));
		}
	}

	public function validateRestoreRevision() {
		parent::validateUpdate();
	}

	public function restoreRevision() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $object) {
			$restoreObject = PageRevisionHandler::getInstance()->getRevisionByID($object->contentID, $this->parameters['restoreObjectID']);
			$this->parameters['data'] = @unserialize($restoreObject->data);
		}

		$this->update();
	}

	public function validateGetRevisions() {
		if (count($this->objectIDs) != 1) {
			throw new UserInputException('objectIDs');
		}
	}

	public function getRevisions() {
		$objectID = reset($this->objectIDs);
		$content = ContentCache::getInstance()->getContent($objectID);
		$revisions = $content->getRevisions();

		WCF::getTPL()->assign(array(
		'revisions' => $revisions,
		'contentID' => $content->contentID
		));

		return array(
			'template' => WCF::getTPL()->fetch('contentRevisionList', 'cms'),
			'revisions' => $revisions,
			'contentID' => $content->contentID
		);
	}
}
