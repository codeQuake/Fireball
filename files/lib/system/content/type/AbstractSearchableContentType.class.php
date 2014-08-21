<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Abstract searchable content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
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
				$data = @unserialize($content->contentData);
				if (is_array($data) && !empty($data)) $this->searchIndexData[$language->languageID][] = $language->get($data[$field]);
			}
			$this->searchIndexData[$language->languageID] = implode("\n", $this->searchIndexData[$language->languageID]);
		}

		return $this->searchIndexData;
	}
}
