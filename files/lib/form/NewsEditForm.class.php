<?php
namespace cms\form;

use cms\data\news\image\NewsImage;
use cms\data\news\News;
use cms\data\news\NewsAction;
use wcf\form\MessageForm;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\HeaderUtil;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class NewsEditForm extends NewsAddForm {
	public $newsID = 0;
	public $news = null;
	public $templateName = 'newsAdd';
	public $action = 'edit';
	public $tags = array();

	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['id'])) $this->newsID = intval($_REQUEST['id']);
		if ($this->newsID == 0) throw new IllegalLinkException();
		
		// set attachment object id
		$this->attachmentObjectID = $this->newsID;
	}

	public function readData() {
		parent::readData();
		$this->news = new News($this->newsID);
		$this->time = $this->news->time;
		$this->subject = $this->news->subject;
		$this->text = $this->news->message;
		$this->enableBBCodes = $this->news->enableBBCodes;
		$this->enableHtml = $this->news->enableHtml;
		$this->enableSmilies = $this->news->enableSmilies;
		$this->image = new NewsImage($this->news->imageID);
		WCF::getBreadcrumbs()->add(new Breadcrumb($this->news->subject, LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'object' => $this->news
		))));
		
		foreach ($this->news->getCategories() as $category) {
			$this->categoryIDs[] = $category->categoryID;
		}
		
		// tagging
		if (MODULE_TAGGING) {
			$tags = $this->news->getTags();
			foreach ($tags as $tag) {
				$this->tags[] = $tag->name;
			}
		}
	}

	public function save() {
		MessageForm::save();
		$data = array(
			'subject' => $this->subject,
			'message' => $this->text,
			'time' => $this->time,
			'enableBBCodes' => $this->enableBBCodes,
			'enableHtml' => $this->enableHtml,
			'imageID' => $this->image->imageID,
			'enableSmilies' => $this->enableSmilies,
			'lastChangeTime' => TIME_NOW,
			'isDisabled' => ($this->time > TIME_NOW) ? 1 : 0,
			'lastEditor' => WCF::getUser()->username,
			'lastEditorID' => WCF::getUser()->userID
		);
		$newsData = array(
			'data' => $data,
			'categoryIDs' => $this->categoryIDs,
			'tags' => $this->tags,
			'attachmentHandler' => $this->attachmentHandler
		);
		
		$action = new NewsAction(array(
			$this->newsID
		), 'update', $newsData);
		$resultValues = $action->executeAction();
		$this->saved();
		
		// re-define after saving
		$this->news = new News($this->newsID);
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'object' => $this->news
		)));
		
		exit();
	}

	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'news' => $this->news,
			'newsID' => $this->newsID
		));
	}
}
