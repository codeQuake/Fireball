<?php

namespace cms\system\dashboard\box;

use cms\data\content\ContentCache;
use cms\data\content\DrainedContentNodeTree;
use wcf\data\dashboard\box\DashboardBox;
use wcf\page\IPage;
use wcf\system\dashboard\box\AbstractSidebarDashboardBox;
use wcf\system\WCF;

class ContentDashboardBox extends AbstractSidebarDashboardBox {
	/**
	 * @var \cms\data\content\DrainedContentNodeTree
	 */
	public $contentNodeTree = null;

	/**
	 * @var integer
	 */
	public $rootContentID = 0;

	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::init()
	 */
	public function init(DashboardBox $box, IPage $page) {
		parent::init($box, $page);

		if (preg_match('/de.codequake.cms.content([\d]+)/', $box->boxName, $matches)) {
			if (!empty($matches[1])) {
				$this->rootContentID = $matches[1];
				$this->contentNodeTree = new DrainedContentNodeTree($this->rootContentID);
			}
		}
	}

	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::render()
	 */
	protected function render() {
		if ($this->contentNodeTree === null) {
			return '';
		}

		return WCF::getTPL()->fetch('contentNodeList', 'cms', array(
			'position' => $this->box->boxType,
			'contentNodeTree' => $this->contentNodeTree
		));
	}

	/**
	 * @see	\wcf\system\dashboard\box\IDashboardBox::getTemplate()
	 */
	public function getTemplate() {
		return $this->render();
	}
}
