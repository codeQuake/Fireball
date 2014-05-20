<?php
namespace cms\data\content;

use cms\system\cache\builder\ContentCacheBuilder;
use cms\system\log\modification\PageModificationLogHandler;
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
	protected $permissionsDelete = array(
		'admin.cms.content.canAddContent'
	);
	protected $requireACP = array(
		'delete',
		'updatePosition'
	);

	public function create() {
		$content = parent::create();
		ContentCacheBuilder::getInstance()->reset();
		PageModificationLogHandler::getInstance()->addContent($content->getPage(), $content);
		return $content;
	}

	public function update() {
		parent::update();
		foreach ($this->objects as $content) {
			PageModificationLogHandler::getInstance()->deleteContent($content->getPage());
		}
		ContentCacheBuilder::getInstance()->reset();
	}

	public function delete() {
		foreach ($this->objects as $content) {
			PageModificationLogHandler::getInstance()->deleteContent($content->getPage());
		}
		parent::delete();
		ContentCacheBuilder::getInstance()->reset();
	}

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
		ContentCacheBuilder::getInstance()->reset();
	}
}
