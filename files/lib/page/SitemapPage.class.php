<?php

namespace cms\page;
use cms\data\page\AccessiblePageNodeTree;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

class SitemapPage extends AbstractPage {
	/**
	 * @var \cms\data\page\AccessiblePageNodeTree
	 */
	public $pageNodeTree = null;
	
	/**
	 * @inheritDoc
	 */
	public function checkPermissions() {
		parent::checkPermissions();
		
		if (!FIREBALL_SITEMAP_ENABLE)
			throw new IllegalLinkException();
	}

	/**
	 * @inheritDoc
	 */
	public function readData () {
		parent::readData();
		
		$this->pageNodeTree = new AccessiblePageNodeTree();
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables () {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
			'pageNodeTree' => $this->pageNodeTree->getIterator()
		]);
	}
}
