<?php

namespace cms\page;
use cms\data\page\AccessiblePageNodeTree;
use wcf\page\AbstractPage;
use wcf\system\WCF;

class SitemapPage extends AbstractPage {

	/**
	 * @see \wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'cms.page.sitemap';

	/**
	 * @var \cms\data\page\AccessiblePageNodeTree
	 */
	public $pageNodeTree = null;

	/**
	 * @see \wcf\page\AbstractPage::readData()
	 */
	public function readData () {
		parent::readData();
		
		$this->pageNodeTree = new AccessiblePageNodeTree();
	}

	public function assignVariables () {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'pageNodeTree' => $this->pageNodeTree->getIterator()
		));
	}
}
