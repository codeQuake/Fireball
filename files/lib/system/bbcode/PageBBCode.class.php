<?php

namespace cms\system\bbcode;
use cms\data\page\PageCache;
use wcf\system\bbcode\AbstractBBCode;
use wcf\system\bbcode\BBCodeParser;
use wcf\system\WCF;

/**
 * Handles the output for the page bbcode.
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageBBCode extends AbstractBBCode {
	public $pageID = 0;
	public $page = null;

	public function getParsedTag(array $openingTag, $content, array $closingTag, BBCodeParser $parser) {
		$this->pageID = $content;
		$this->page = PageCache::getInstance()->getPage($this->pageID);
		
		if ($this->page == null)
			return WCF::getLanguage()->get('cms.page.bbcode.notFound');
		
		return '<a href="' . $this->page->getLink() . '" class="pagePreview">' . $this->page->getTitle() . '</a>';
	}
}
