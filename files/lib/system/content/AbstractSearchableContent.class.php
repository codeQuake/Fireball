<?php
namespace cms\system\content;
use wcf\system\event\EventHandler;

/**
 * Abstract searchable content implementation.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractSearchableContent extends AbstractContent implements ISearchableContent {
	/**
	 * search index data
	 * @var	array<array>
	 */
	public $searchIndexData = array();

	/**
	 * @see	\cms\system\content\ISearchableContent::getSearchIndexData()
	 */
	public function getSearchIndexData() {
		// call 'getSearchIndexData' event
		EventHandler::getInstance()->fireAction($this, 'getSearchIndexData');

		$searchIndexData = array();
		foreach ($this->searchIndexData as $languageID => $values) {
			$searchIndexData[$languageID] = implode("\n", $values);
		}

		return $searchIndexData;
	}
}
