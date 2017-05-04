<?php

namespace cms\system\clipboard\action;

use cms\data\content\ContentAction;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\WCF;

/**
 * Prepares clipboard editor items for content objects.
 *
 * @author           Florian Frantzen, Florian Gail
 * @copyright        2013 - 2017 codeQuake
 * @license          GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package          de.codequake.cms
 *
 * @property \cms\data\content\Content[] $objects
 */
class ContentClipboardAction extends AbstractClipboardAction {
	/**
	 * @inheritDoc
	 */
	protected $actionClassActions = ['delete', 'disable', 'enable'];
	
	/**
	 * @inheritDoc
	 */
	protected $supportedActions = ['delete', 'disable', 'enable', 'copyTo', 'moveTo'];
	
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
			case 'copyTo':
			case 'moveTo':
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.codequake.cms.content.' . $action->actionName . '.confirmMessage', [
					'count' => $item->getCount(),
					'contents' => $this->objects
				]));
				break;
			case 'delete':
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.codequake.cms.content.delete.confirmMessage', [
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
		return ContentAction::class;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTypeName() {
		return 'de.codequake.cms.content';
	}
	
	/**
	 * Returns the ids of the contents which can be deleted.
	 *
	 * @return        array<integer>
	 */
	protected function validateDelete() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.fireball.content.canAddContent')) {
			return [];
		}
		
		return array_keys($this->objects);
	}
	
	/**
	 * Returns the ids of the contents which can be disabled.
	 *
	 * @return        array<integer>
	 */
	protected function validateDisable() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.fireball.content.canAddContent')) {
			return [];
		}
		
		$contentIDs = [];
		foreach ($this->objects as $content) {
			if (!$content->isDisabled) $contentIDs[] = $content->contentID;
		}
		
		return $contentIDs;
	}
	
	/**
	 * Returns the ids of the contents which can be enabled.
	 *
	 * @return        array<integer>
	 */
	protected function validateEnable() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.fireball.content.canAddContent')) {
			return [];
		}
		
		$contentIDs = [];
		foreach ($this->objects as $content) {
			if ($content->isDisabled) $contentIDs[] = $content->contentID;
		}
		
		return $contentIDs;
	}
	
	/**
	 * Returns the ids of the contents which can be copied.
	 *
	 * @return        integer[]
	 */
	protected function validateCopyTo() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.fireball.content.canAddContent')) {
			return [];
		}
		
		return array_keys($this->objects);
	}
	
	/**
	 * Returns the ids of the contents which can be moved.
	 *
	 * @return        integer[]
	 */
	protected function validateMoveTo() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.fireball.content.canAddContent')) {
			return [];
		}
		
		return array_keys($this->objects);
	}
}
