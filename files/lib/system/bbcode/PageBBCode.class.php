<?php

namespace cms\system\bbcode;
use cms\data\page\Page;
use cms\data\page\PageCache;
use wcf\system\bbcode\AbstractBBCode;
use wcf\system\bbcode\BBCodeParser;
use wcf\system\WCF;

/**
 * Handles the output for the page bbcode.
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageBBCode extends AbstractBBCode {
	public $pageID = 0;
	public $page = null;
	
	/**
	 * @inheritDoc
	 */
	public function getParsedTag(array $openingTag, $content, array $closingTag, BBCodeParser $parser) {
		if (isset($openingTag['attributes'][0])) {
			$this->pageID = $openingTag['attributes'][0];
		}
		$this->page = PageCache::getInstance()->getPage($this->pageID);
		if ($this->page === null)
			$this->page = new Page($this->pageID);
		
		if ($this->page == null && empty($content))
			return WCF::getLanguage()->get('cms.page.bbcode.notFound');
		else if ($this->page == null && !empty($content))
			return $content . WCF::getLanguage()->get('cms.page.bbcode.notFound.inline');
		
		return '<a href="' . $this->page->getLink() . '" class="pagePreview">' . (!empty($content) ? $content : $this->page->getTitle()) . '</a>';
	}
}
