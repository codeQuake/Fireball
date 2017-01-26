<?php

namespace cms\system\importer;
use cms\data\stylesheet\Stylesheet;
use cms\data\stylesheet\StylesheetAction;
use wcf\system\importer\AbstractImporter;
use wcf\system\importer\ImportHandler;

/**
 * Provides an importer for stylesheets
 *
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetImporter extends AbstractImporter {
	/**
	 * @inheritDoc
	 */
	protected $className = Stylesheet::class;

	/**
	 * @inheritDoc
	 */
	public function import($oldID, array $data, array $additionalData = []) {
		unset($data['stylesheetID']);

		if (is_numeric($oldID)) {
			$stylesheet = new Stylesheet($oldID);
			if (!$stylesheet->stylesheetID)
				$data['stylesheetID'] = $oldID;
		}

		$action = new StylesheetAction([], 'create', [
			'data' => $data
		]);
		$returnValues = $action->executeAction();
		$newID = $returnValues['returnValues']->stylesheetID;
		$stylesheet = new Stylesheet($newID);

		ImportHandler::getInstance()->saveNewID('de.codequake.cms.stylesheet', $oldID, $stylesheet->stylesheetID);

		return $stylesheet->stylesheetID;
	}
}
