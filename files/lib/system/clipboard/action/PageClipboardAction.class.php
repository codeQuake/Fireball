<?php
namespace cms\system\clipboard\action;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Prepares clipboard editor items for page objects.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageClipboardAction extends AbstractClipboardAction {
	/**
	 * @see	\wcf\system\clipboard\action\AbstractClipboardAction::$actionClassActions
	 */
	protected $actionClassActions = array('delete', 'disable', 'enable');

	/**
	 * @see	\wcf\system\clipboard\action\AbstractClipboardAction::$supportedActions
	 */
	protected $supportedActions = array('delete', 'disable', 'enable');

	/**
	 * @see	\wcf\system\clipboard\action\IClipboardAction::execute()
	 */
	public function execute(array $objects, ClipboardAction $action) {
		$item = parent::execute($objects, $action);

		if ($item === null) {
			return null;
		}

		// handle actions
		switch ($action->actionName) {
			case 'delete':
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.codequake.cms.page.delete.confirmMessage', array(
					'count' => $item->getCount()
				)));
			break;
		}

		return $item;
	}

	/**
	 * @see	\wcf\system\clipboard\action\IClipboardAction::getClassName()
	 */
	public function getClassName() {
		return 'cms\data\page\PageAction';
	}

	/**
	 * @see	\wcf\system\clipboard\action\IClipboardAction::getTypeName()
	 */
	public function getTypeName() {
		return 'de.codequake.cms.page';
	}

	/**
	 * Returns the ids of the pages which can be deleted.
	 * 
	 * @return	array<integer>
	 */
	protected function validateDelete() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.cms.page.canAddPage')) {
			return array();
		}

		return array_keys($this->objects);
	}

	/**
	 * Returns the ids of the pages which can be disabled.
	 * 
	 * @return	array<integer>
	 */
	protected function validateDisable() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.cms.page.canAddPage')) {
			return array();
		}

		$pageIDs = array();
		foreach ($this->objects as $page) {
			if (!$page->isDisabled) $pageIDs[] = $page->pageID;
		}

		return $pageIDs;
	}

	/**
	 * Returns the ids of the pages which can be enabled.
	 * 
	 * @return	array<integer>
	 */
	protected function validateEnable() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.cms.page.canAddPage')) {
			return array();
		}

		$pageIDs = array();
		foreach ($this->objects as $page) {
			if ($page->isDisabled) $pageIDs[] = $page->pageID;
		}

		return $pageIDs;
	}
}
