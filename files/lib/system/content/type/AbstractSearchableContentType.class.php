<?php
namespace cms\system\content\type;

use cms\data\content\Content;
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
