<?php

namespace cms\data\content\match;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\dashboard\box\DashboardBoxAction;

/**
 * @author	Florian Gail
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentBoxMatchAction extends AbstractDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'cms\data\content\match\ContentBoxMatchEditor';

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::delete()
	 */
	public function delete() {
		$returnValues = parent::delete();

		/** @var ContentBoxMatchEditor $matchEditor */
		foreach ($this->objects as $matchEditor) {
			$boxAction = new DashboardBoxAction(array($matchEditor->boxID), 'delete');
			$boxAction->executeAction();
		}

		return $returnValues;
	}
}
