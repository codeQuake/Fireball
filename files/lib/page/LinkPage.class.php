<?php

namespace cms\page;
use cms\data\page\PageEditor;
use cms\system\counter\VisitCountHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * redirects to a given url
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class LinkPage extends AbstractCMSPage {
	/**
	 * enables template usage
	 * @var	string
	 */
	public $useTemplate = false;

	/**
	 * @see	\wcf\page\IPage::show()
	 */
	public function show() {
		// register visit
		VisitCountHandler::getInstance()->count();

		// count click
		$pageEditor = new PageEditor($this->page);
		$pageEditor->updateCounters(array(
			'clicks' => 1
		));

		if ($this->page->delayedRedirect) {
			if ($this->page->redirectMessage)
				HeaderUtil::delayedRedirect($this->page->url, WCF::getLanguage()->get($this->page->redirectMessage), $this->page->delay);
			else
				HeaderUtil::delayedRedirect($this->page->url, WCF::getLanguage()->get('cms.page.link.redirect'), $this->page->delay);
		} else {
			HeaderUtil::redirect($this->page->url);
		}
	}
}
