<?php
namespace cms\system\clipboard\action;

use cms\data\file\FileAction;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\WCF;

/**
 * Prepares clipboard editor items for file objects.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileClipboardAction extends AbstractClipboardAction {
	/**
	 * @inheritDoc
	 */
	protected $actionClassActions = ['delete'];

	/**
	 * @inheritDoc
	 */
	protected $supportedActions = ['delete'];

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
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.codequake.cms.file.delete.confirmMessage', [
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
		return FileAction::class;
	}

	/**
	 * @inheritDoc
	 */
	public function getTypeName() {
		return 'de.codequake.cms.file';
	}

	/**
	 * Returns the ids of the files which can be deleted.
	 * 
	 * @return	array<integer>
	 */
	protected function validateDelete() {
		if (WCF::getSession()->getPermission('admin.fireball.file.canAddFile')) {
			return array_keys($this->objects);
		}

		return [];
	}
}
