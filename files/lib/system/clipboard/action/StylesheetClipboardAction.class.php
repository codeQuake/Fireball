<?php
namespace cms\system\clipboard\action;

use cms\data\stylesheet\StylesheetAction;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\WCF;

/**
 * Prepares clipboard editor items for stylesheet objects.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetClipboardAction extends AbstractClipboardAction {
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
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.codequake.cms.stylesheet.delete.confirmMessage', [
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
		return StylesheetAction::class;
	}

	/**
	 * @inheritDoc
	 */
	public function getTypeName() {
		return 'de.codequake.cms.stylesheet';
	}

	/**
	 * Returns the ids of the stylesheets which can be deleted.
	 * 
	 * @return	array<integer>
	 */
	protected function validateDelete() {
		if (WCF::getSession()->getPermission('admin.fireball.style.canAddStylesheet')) {
			return array_keys($this->objects);
		}

		return [];
	}
}
