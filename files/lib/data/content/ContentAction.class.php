<?php
namespace cms\data\content;

use cms\system\cache\builder\ContentCacheBuilder;
use cms\system\revision\ContentRevisionHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IClipboardAction;
use wcf\data\ISortableAction;
use wcf\data\IToggleAction;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * Executes content-related actions.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentAction extends AbstractDatabaseObjectAction implements IClipboardAction, ISortableAction, IToggleAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'cms\data\content\ContentEditor';

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$resetCache
	 */
	protected $resetCache = array('copy', 'create', 'delete', 'disable', 'enable', 'restoreRevision', 'toggle', 'update', 'updatePosition');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.cms.content.canAddContent');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.cms.content.canAddContent');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$requireACP
	 */
	protected $requireACP = array('delete', 'updatePosition');

	/**
	 * Validates parameters to copy a content.
	 */
	public function validateCopy() {
		// validate 'objectIDs' parameter
		$this->getSingleObject();
	}

	/**
	 * Copies a specific content.
	 */
	public function copy() {
		$object = $this->getSingleObject();
		$data = $object->getDecoratedObject()->getData();
		$childs = $object->getDecoratedObject()->getChildren();

		$oldID = $data['contentID'];
		unset($data['contentID']);
		$this->parameters['data'] = $data;
		$content = $this->create();
		$contentID = $content->contentID;
		$tmp = array();
		$tmp[$oldID] = $contentID;
		$affectedIDs = array();

		foreach ($childs as $child) {
			$childID = $child->getDecoratedObject()->contentID;

			$data = $child->getDecoratedObject()->getData();
			unset($data['contentID']);
			$this->parameters['data'] = $data;
			$new = $this->create();
			$tmp[$childID] = $new->contentID;
			$affectedIDs[] = $new->contentID;
		}

		foreach ($affectedIDs as $affectedID) {
			$update = array();
			$affectedObject = new Content($affectedID);
			if (isset ($tmp[$affectedObject->parentID])) {
				$editor = new ContentEditor($affectedObject);
				$update['parentID'] = $tmp[$affectedObject->parentID];
				$editor->update($update);
			}
		}
	}

	/**
	 * Creates a revision.
	 */
	protected function createRevision() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		$action = 'create';
		if (isset($this->parameters['action'])) {
			$action = $this->parameters['action'];
		}

		foreach ($this->objects as $object) {
			call_user_func(array(
				$this->className,
				'createRevision'
			), array(
				'contentID' => $object->getObjectID(),
				'action' => $action,
				'userID' => WCF::getUser()->userID,
				'username' => WCF::getUser()->username,
				'time' => TIME_NOW,
				'data' => serialize($object->getDecoratedObject()->getData())
			));
		}
	}

	/**
	 * Validates permissions to disable contents.
	 */
	public function validateDisable() {
		$this->validateUpdate();
	}

	/**
	 * Disables contents.
	 */
	public function disable() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $contentEditor) {
			$contentEditor->update(array('isDisabled' => 1));
		}
	}

	/**
	 * Validates permissions to enable contents.
	 */
	public function validateEnable() {
		$this->validateUpdate();
	}

	/**
	 * Enables contents.
	 */
	public function enable() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $contentEditor) {
			$contentEditor->update(array('isDisabled' => 0));
		}
	}

	/**
	 * Validate parameters to get a list of content revisions.
	 */
	public function validateGetRevisions() {
		if (count($this->objectIDs) != 1) {
			throw new UserInputException('objectIDs');
		}
	}

	/**
	 * Returns a formatted list of content revisions.
	 */
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

	/**
	 * Validates parameters and permissions to restore a revision.
	 */
	public function validateRestoreRevision() {
		$this->validateUpdate();
	}

	/**
	 * Restores a content revision.
	 */
	public function restoreRevision() {
		if (empty($this->objects)) {
			$this->readObjects();
		}

		foreach ($this->objects as $object) {
			$restoreObject = ContentRevisionHandler::getInstance()->getRevisionByID($object->contentID, $this->parameters['restoreObjectID']);
			$this->parameters['data'] = @unserialize($restoreObject->data);
		}

		$this->update();
	}

	/**
	 * @see	\wcf\data\IToggleAction::validateToggle()
	 */
	public function validateToggle() {
		$this->validateUpdate();
	}

	/**
	 * @see	\wcf\data\IToggleAction::toggle()
	 */
	public function toggle() {
		if (empty($this->objects)) $this->readObjects();

		foreach ($this->objects as $content) {
			$content->update(array(
				'isDisabled' => 1 - $content->isDisabled
			));
		}
	}

	/**
	 * @see	\wcf\data\IClipboardAction::validateUnmarkAll()
	 */
	public function validateUnmarkAll() { /* nothing */ }

	/**
	 * @see	\wcf\data\IClipboardAction::unmarkAll()
	 */
	public function unmarkAll() {
		ClipboardHandler::getInstance()->removeItems(ClipboardHandler::getInstance()->getObjectTypeID('de.codequake.cms.content'));
	}

	/**
	 * @see	\wcf\data\ISortableAction::validateUpdatePosition()
	 */
	public function validateUpdatePosition() {
		WCF::getSession()->checkPermissions(array(
			'admin.cms.content.canAddContent'
		));

		if (!isset($this->parameters['data']['structure']) || !is_array($this->parameters['data']['structure'])) {
			throw new UserInputException('structure');
		}
		$contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
		foreach ($this->parameters['data']['structure'] as $parentID => $contentIDs) {
			if ($parentID) {
				if (!isset($contents[$parentID])) {
					throw new UserInputException('structure');
				}

				$this->objects[$parentID] = new ContentEditor($contents[$parentID]);
			}

			foreach ($contentIDs as $contentID) {
				if (!isset($contents[$contentID])) {
					throw new UserInputException('structure');
				}

				$this->objects[$contentID] = new ContentEditor($contents[$contentID]);
			}
		}
	}

	/**
	 * @see	\wcf\data\ISortableAction::updatePosition()
	 */
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

		// create revision
		$this->parameters['action'] = 'updatePosition';
		$this->createRevision();
	}
}
