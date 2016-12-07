<?php
namespace cms\data\content;

use cms\data\page\PageAction;
use cms\data\page\PageCache;
use cms\system\cache\builder\ContentCacheBuilder;
use wcf\data\object\type\ObjectTypeCache;
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
 * @copyright	2013 - 2015 codeQuake
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
	protected $resetCache = array('copy', 'create', 'delete', 'disable', 'enable', 'toggle', 'update', 'updatePosition', 'frontendCreate');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.fireball.content.canAddContent');

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.fireball.content.canAddContent');

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
		$data['contentData'] = serialize($data['contentData']);
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
			$data['contentData'] = serialize($data['contentData']);
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
	 * @see	\wcf\data\AbstractDatabaseObjectAction::create()
	 */
	public function create() {
		// serialize content data
		if (isset($this->parameters['data']['contentData']) && is_array($this->parameters['data']['contentData'])) {
			$this->parameters['data']['contentData'] = serialize($this->parameters['data']['contentData']);
		}

		return parent::create();
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
	 * Validates parameters to create a content in frontend
	 */
	 public function validateFrontendCreate() {
		//check permission
		if (!WCF::getSession()->getPermission('admin.fireball.content.canAddContent')) throw new AJAXException();
		//TODO I18n & other error handling
		if (!isset($this->parameters['data']['parentID'])) $this->parameters['data']['parentID'] = null;
	 }
	 
	/**
	 * performs frontend create
	 */
	public function frontendCreate() {
		$data = $this->parameters['data'];
		//check foreigns
		if ($data['parentID'] == 0) $data['parentID'] = null;
		
		//content type
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.content.type', $data['objectType']);
		if ($objectType === null || !$objectType->getProcessor()->isAvailableToAdd()) {
			throw new UserInputException('objectType');
		}
		$data['contentTypeID'] = $objectType->objectTypeID;
		
		//unset unused
		unset($data['t']);
		unset($data['objectType']);
		
		//unset bullshit
		unset($data['undefined']);
		
		//set params
		$this->parameters['data'] = $data;
		
		//finally create new page
		$content = $this->create();
		return array(
			'content' => $content,
			'output' => $content->getOutput(),
			'parentID' => $content->parentID ?: 0
			);
	}
	
	/**
	 * Validates parameters to get the content add  dialog.
	 */
	public function validateGetAddDialog() {
		//check permission
		if (!WCF::getSession()->getPermission('admin.fireball.content.canAddContent')) throw new AJAXException();
		
		//validate position
		if (!isset($this->parameters['position']) || !in_array($this->parameters['position'], array('body', 'sidebar'))) throw new UserInputException('position');
		
		//validate parent
		if (isset($this->parameters['parentID']) && $this->parameters['parentID'] != 0) {
			$content = ContentCache::getInstance()->getContent($this->parameters['parentID']);
			if($content === null || $content->contentID == 0) throw new UserInputException('parentID');
		}
		
		//TODO validate content type & content
	}

	/**
	 * Returns the content add dialog.
	 */
	public function getAddDialog() {
		$page = PageCache::getInstance()->getPage($this->parameters['pageID']);
		if (isset($this->parameters['contentID'])) {
			//TODO
		}
		
		//get initial data (TODO: EDIT)
		$action = 'add';
		$contentData = array();
		$cssClasses = '';
		$pageID = $page->pageID;
		if (isset($this->parameters['parentID']) && $this->parameters['parentID'] != 0) {
			$parent = ContentCache::getInstance()->getContent($this->parameters['parentID']);
			$parentID = $parent->contentID;
		}
		else $parentID = 0;
		$position = $this->parameters['position'];
		$showOrder = 0;
		$title = '';
		
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.content.type', $this->parameters['type']);
		if ($objectType === null || !$objectType->getProcessor()->isAvailableToAdd()) {
			throw new UserInputException('objectType');
		}
		
		WCF::getTPL()->assign(array(
			'action' => $action,
			'contentData' => $contentData,
			'cssClasses' => $cssClasses,
			'pageID' => $pageID,
			'parentID' => $parentID,
			'position' => $position,
			'showOrder' => $showOrder,
			'title' => $title,
			'objectType' => $objectType
		));

		return array(
			'template' => WCF::getTPL()->fetch('contentAddDialog', 'cms')
		);
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
	 * @see	\wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
		// serialize content data
		if (isset($this->parameters['data']['contentData']) && is_array($this->parameters['data']['contentData'])) {
			$this->parameters['data']['contentData'] = serialize($this->parameters['data']['contentData']);
		}

		parent::update();
	}

	/**
	 * @see	\wcf\data\ISortableAction::validateUpdatePosition()
	 */
	public function validateUpdatePosition() {
		WCF::getSession()->checkPermissions(array(
			'admin.fireball.content.canAddContent'
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

		$pageIDs = array();
		foreach ($this->parameters['data']['structure'] as $parentID => $contentIDs) {
			$position = 1;
			foreach ($contentIDs as $contentID) {
				if (!in_array($this->objects[$contentID]->pageID, $pageIDs)) {
					$pageIDs[] = $this->objects[$contentID]->pageID;
				}

				$this->objects[$contentID]->update(array(
					'parentID' => $parentID != 0 ? $this->objects[$parentID]->contentID : null,
					'showOrder' => $position ++
				));
			}
		}

		WCF::getDB()->commitTransaction();

		// create revision
		$pageAction = new PageAction($pageIDs, 'createRevision', array(
			'action' => 'content.updatePosition'
		));
		$pageAction->executeAction();
	}
}
