<?php
namespace cms\system\content\type;

use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * abstract for searchable content types
 *
 * @author Jens Krumsieck
 * @copyright codeQuake 2014
 * @package de.codequake.cms
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
abstract class AbstractSearchableContentType extends AbstractContentType implements ISearchableContentType{

	protected $searchableFields = array();
	public $searchIndexData = array();

	public function getSearchableData() {
		foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
			foreach ($this->searchableFields as $field) {
				$this->searchIndexData[$language->languageID][] = $language->get($this->{$field});
			}
			$this->searchIndexData[$language->languageID] = implode("\n", $this->searchIndexData[$language->languageID]);
		}

		return $this->searchIndexData;
	}
}
