<?php
namespace cms\form;

use cms\data\category\NewsCategory;
use cms\data\category\NewsCategoryNodeTree;
use cms\data\news\image\NewsImage;
use cms\data\news\NewsAction;
use cms\data\news\NewsEditor;
use wcf\form\MessageForm;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\category\CategoryHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\poll\PollManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows the news add form.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsAddForm extends MessageForm {
	public $action = 'add';
	public $neededModules = array(
		'MODULE_NEWS'
	);
	public $categoryIDs = array();
	public $categoryList = array();
	public $activeMenuItem = 'cms.page.news';
	public $enableTracking = true;
	public $neededPermissions = array(
		'user.cms.news.canAddNews'
	);
	public $enableMultilingualism = true;
	public $attachmentObjectType = 'de.codequake.cms.news';
	public $image = null;
	public $time = TIME_NOW;
	public $teaser = '';
	public $tags = array();

	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_POST['tags']) && is_array($_POST['tags'])) $this->tags = ArrayUtil::trim($_POST['tags']);
		if (isset($_POST['time']) && $_POST['time'] != 0) $this->time = strtotime($_POST['time']);
		if (isset($_POST['imageID'])) $this->image = new NewsImage(intval($_POST['imageID']));
		if (isset($_POST['teaser'])) $this->teaser = StringUtil::trim($_POST['teaser']);

		if (MODULE_POLL && WCF::getSession()->getPermission('user.cms.news.canStartPoll')) PollManager::getInstance()->readFormParameters();
	}

	public function readParameters() {
		parent::readParameters();
		// polls
		if (MODULE_POLL & WCF::getSession()->getPermission('user.cms.news.canStartPoll')) PollManager::getInstance()->setObject('de.codequake.cms.news', 0);
		if (isset($_REQUEST['categoryIDs']) && is_array($_REQUEST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_REQUEST['categoryIDs']);
	}

	public function readData() {
		parent::readData();

		WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('cms.page.news'), LinkHandler::getInstance()->getLink('NewsCategoryList', array(
			'application' => 'cms'
		))));

		$excludedCategoryIDs = array_diff(NewsCategory::getAccessibleCategoryIDs(), NewsCategory::getAccessibleCategoryIDs(array(
			'canAddNews'
		)));
		$categoryTree = new NewsCategoryNodeTree('de.codequake.cms.category.news', 0, false, $excludedCategoryIDs);
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);

		// default values
		if (! count($_POST)) {
			$this->username = WCF::getSession()->getVar('username');

			// multilingualism
			if (! empty($this->availableContentLanguages)) {
				if (! $this->languageID) {
					$language = LanguageFactory::getInstance()->getUserLanguage();
					$this->languageID = $language->languageID;
				}

				if (! isset($this->availableContentLanguages[$this->languageID])) {
					$languageIDs = array_keys($this->availableContentLanguages);
					$this->languageID = array_shift($languageIDs);
				}
			}
		}
	}

	public function validate() {
		parent::validate();
		// categories
		if (empty($this->categoryIDs)) {
			throw new UserInputException('categoryIDs');
		}

		foreach ($this->categoryIDs as $categoryID) {
			$category = CategoryHandler::getInstance()->getCategory($categoryID);
			if ($category === null) throw new UserInputException('categoryIDs');

			$category = new NewsCategory($category);
			if (! $category->isAccessible() || ! $category->getPermission('canAddNews')) throw new UserInputException('categoryIDs');
		}

		if (MODULE_POLL && WCF::getSession()->getPermission('user.cms.news.canStartPoll')) PollManager::getInstance()->validate();
	}

	public function save() {
		parent::save();
		if ($this->languageID === null) {
			$this->languageID = LanguageFactory::getInstance()->getDefaultLanguageID();
		}
		$data = array(
			'languageID' => $this->languageID,
			'subject' => $this->subject,
			'time' => $this->time,
			'teaser' => $this->teaser,
			'message' => $this->text,
			'userID' => WCF::getUser()->userID,
			'username' => WCF::getUser()->username,
			'isDisabled' => ($this->time > TIME_NOW) ? 1 : 0,
			'enableBBCodes' => $this->enableBBCodes,
			'showSignature'	=> $this->showSignature,
			'enableHtml' => $this->enableHtml,
			'enableSmilies' => $this->enableSmilies,
			'imageID' => $this->image->imageID,
			'lastChangeTime' => TIME_NOW
		);
		$newsData = array(
			'data' => $data,
			'tags' => array(),
			'attachmentHandler' => $this->attachmentHandler,
			'categoryIDs' => $this->categoryIDs
		);
		$newsData['tags'] = $this->tags;

		$action = new NewsAction(array(), 'create', $newsData);
		$resultValues = $action->executeAction();

		// save polls
		if (WCF::getSession()->getPermission('user.cms.news.canStartPoll') && MODULE_POLL) {
			$pollID = PollManager::getInstance()->save($resultValues['returnValues']->newsID);
			if ($pollID) {
				$editor = new NewsEditor($resultValues['returnValues']);
				$editor->update(array(
					'pollID' => $pollID
				));

			}
		}

		$this->saved();

		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'object' => $resultValues['returnValues']
		)));
		exit();
	}

	public function assignVariables() {
		parent::assignVariables();

		if (WCF::getSession()->getPermission('user.cms.news.canStartPoll') && MODULE_POLL) PollManager::getInstance()->assignVariables();

		WCF::getTPL()->assign(array(
			'categoryList' => $this->categoryList,
			'categoryIDs' => $this->categoryIDs,
			'image' => $this->image,
			'teaser' => $this->teaser,
			'imageID' => isset($this->image->imageID) ? $this->image->imageID : 0,
			'time' => gmdate("Y-m-d H:i", $this->time),
			'action' => $this->action,
			'tags' => $this->tags,
			'allowedFileExtensions' => explode("\n", StringUtil::unifyNewlines(WCF::getSession()->getPermission('user.cms.news.allowedAttachmentExtensions')))
		));
	}
}
