<?php
namespace cms\system\content;
use wcf\data\package\PackageCache;
use wcf\system\language\I18nHandler;

/**
 * Headline content implementation.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class HeadlineContent extends AbstractSearchableContent {
	/**
	 * @see	\cms\system\content\IContent::setI18nOptions()
	 */
	public function setI18nOptions() {
		parent::setI18nOptions();

		I18nHandler::getInstance()->setOptions('content_'.$this->contentID.'_text', PackageCache::getInstance()->getPackageID('de.codequake.cms'), $this->text, 'cms.content.text\d+');
	}

	/**
	 * @see	\cms\system\content\ISearchableContent::getSearchIndexData()
	 */
	public function getSearchIndexData() {
		foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
			$text = $language->get($this->text);
			$this->searchIndexData[$language->languageID][] = $text;
		}

		return parent::getSearchIndexData();
	}
}
