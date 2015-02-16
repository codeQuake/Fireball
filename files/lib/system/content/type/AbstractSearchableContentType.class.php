<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\language\LanguageFactory;

/**
 * Abstract searchable content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractSearchableContentType extends AbstractContentType implements ISearchableContentType {
	/**
	 * list of searchable fields
	 * @var	array<string>
	 */
	protected $searchableFields = array();

	/**
	 * search index data
	 * @var	array<array>
	 */
	public $searchIndexData = array();

	/**
	 * @see	\cms\system\content\type\ISearchableContentType::getSearchableData()
	 */
	public function getSearchableData(Content $content) {
		foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
			$this->searchIndexData[$language->languageID] = array();

			foreach ($this->searchableFields as $field) {
				$this->searchIndexData[$language->languageID][] = $language->get($content->{$field});
			}

			$this->searchIndexData[$language->languageID] = implode("\n", $this->searchIndexData[$language->languageID]);
		}

		return $this->searchIndexData;
	}
}
