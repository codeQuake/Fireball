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
		return $content;
	}

	public function update() {
		parent::update();
		ContentCacheBuilder::getInstance()->reset();
	}

	public function delete() {
		parent::delete();
		ContentCacheBuilder::getInstance()->reset();
	}

	public function validateUpdatePosition() {
		WCF::getSession()->checkPermissions(array(
			'admin.cms.content.canAddContentSection'
		));

		// check parameters
		if (! isset($this->parameters['data']['structure'])) {
			throw new SystemException("Missing 'structure' parameter.");
		}
		if (! is_array($this->parameters['data']['structure'])) {
			throw new SystemException("'structure' parameter is no array.");
		}

		$itemIDs = array();
		foreach ($this->parameters['data']['structure'] as $items) {
			$itemIDs = array_merge($itemIDs, $items);
		}

		// createList
		$list = new ContentList();
		$list->getConditionBuilder()->add('content.contentID IN (?)', array(
			$itemIDs
		));
		$list->readObjects();
		$this->items = $list->getObjects();

		// check number of items
		if (count($items) != count($itemIDs)) {
			throw new UserInputException('structure');
		}
	}

	public function updatePosition() {
		$sql = "UPDATE cms" . WCF_N . "_content
                SET showOrder = ?
                WHERE contentID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($this->parameters['data']['structure'] as $parentContentID => $contentIDs) {
			foreach ($contentIDs as $showOrder => $contentID) {
				$this->items[$contentID]->getEditor()->update(array(
					'showOrder' => $showOrder + 1
				));
			}
		}
		WCF::getDB()->commitTransaction();
		ContentCacheBuilder::getInstance()->reset();
	}
}
