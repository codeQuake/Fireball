<?php
namespace cms\system\clipboard\action;
use cms\data\page\PageAction;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Prepares clipboard editor items for page objects.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageClipboardAction extends AbstractClipboardAction {
	/**
	 * @inheritDoc
	 */
	protected $actionClassActions = ['delete', 'disable', 'enable'];

	/**
	 * @inheritDoc
	 */
	protected $supportedActions = ['delete', 'disable', 'enable'];

	/**
	 * @inheritDoc
	 */
	public function execute(array $objects, ClipboardAction $action) {
		$item = parent::execute($objects, $action);

		if ($item === null) {
			return null;
		}

		// handle actions
		switch ($action->actionName) {
			case 'delete':
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.codequake.cms.page.delete.confirmMessage', [
					'count' => $item->getCount()
				]));
			break;
		}

		return $item;
	}

	/**
	 * @inheritDoc
	 */
	public function getClassName() {
		return PageAction::class;
	}

	/**
	 * @inheritDoc
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
		$pageIDs = [];
		foreach ($this->objects as $page) {
			if ($page->canDelete()) {
				$pageIDs[] = $page->pageID;
			}
		}

		return $pageIDs;
	}

	/**
	 * Returns the ids of the pages which can be disabled.
	 * 
	 * @return	array<integer>
	 */
	protected function validateDisable() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.fireball.page.canAddPage')) {
			return [];
		}

		$pageIDs = [];
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
		if (!WCF::getSession()->getPermission('admin.fireball.page.canAddPage')) {
			return [];
		}

		$pageIDs = [];
		foreach ($this->objects as $page) {
			if ($page->isDisabled) $pageIDs[] = $page->pageID;
		}

		return $pageIDs;
	}
}
