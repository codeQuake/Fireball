<?php
namespace cms\acp\form;

use cms\data\feed\FeedAction;
use cms\data\news\image\NewsImage;
use cms\data\news\image\NewsImageList;
use wcf\form\AbstractForm;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;
use wcf\util\HTTPRequest;
use wcf\util\StringUtil;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms.news.rss
 */
class FeedAddForm extends AbstractForm {
	public $templateName = 'feedAdd';
	public $neededPermissions = array(
		'admin.cms.news.canAddFeed'
	);
	public $activeMenuItem = 'cms.acp.menu.link.cms.feed.add';
	public $title = '';
	public $feedUrl = '';
	public $categoryID = 0;
	public $languageID = null;
	public $image = null;
	public $imageList;
	public $availableContentLanguages = array();
	public $categoryList = null;

	public function readData() {
		parent::readData();
		$this->availableContentLanguages = LanguageFactory::getInstance()->getContentLanguages();
		$this->categoryList = CategoryHandler::getInstance()->getCategories('de.codequake.cms.category.news'); // news images
		$list = new NewsImageList();
		$list->readObjects();
		$this->imageList = $list->getObjects();
	}

	public function readFormParameters() {
		parent::readFormParameters();
		
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['feedUrl'])) $this->feedUrl = StringUtil::trim($_POST['feedUrl']);
		if (isset($_POST['categoryID'])) $this->categoryID = intval($_POST['categoryID']);
		if (isset($_POST['languageID'])) $this->languageID = intval($_POST['languageID']);
		if (isset($_POST['imageID'])) $this->image = new NewsImage(intval($_POST['imageID']));
	}

	public function save() {
		parent::save();
		
		$objectAction = new FeedAction(array(), 'create', array(
			'data' => array(
				'title' => $this->title,
				'feedUrl' => $this->feedUrl,
				'lastCheck' => TIME_NOW,
				'categoryID' => $this->categoryID,
				'languageID' => $this->languageID,
				'imageID' => $this->image->imageID
			)
		));
		$objectAction->executeAction();
		
		$this->saved();
		WCF::getTPL()->assign('success', true);
		
		$this->title = '';
		$this->feedUrl = '';
	}

	public function validate() {
		parent::validate();
		if ($this->feedUrl == "") throw new UserInputException('feedUrl', 'empty');
		try {
			$request = new HTTPRequest($this->feedUrl);
			$request->execute();
			$feedData = $request->getReply();
			$feedData = $feedData['body'];
		}
		catch (\wcf\system\exception\SystemException $e) {
			// invalid URL
			return (array(
				'errorMessage' => $e->getMessage()
			));
		}
		if (! simplexml_load_string($feedData)) throw new UserInputException('feedUrl', 'noFeed');
	}

	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'categories' => $this->categoryList,
			'title' => $this->title,
			'feedUrl' => $this->feedUrl,
			'action' => 'add',
			'availableContentLanguages' => $this->availableContentLanguages,
			'categoryID' => $this->categoryID,
			'imageID' => isset($this->image->imageID) ? $this->image->imageID : 0,
			'image' => $this->image,
			'languageID' => $this->languageID,
			'imageList' => $this->imageList
		));
	}
}
