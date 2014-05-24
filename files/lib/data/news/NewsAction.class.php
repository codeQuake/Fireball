<?php
namespace cms\data\news;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IClipboardAction;
use wcf\system\attachment\AttachmentHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\search\SearchIndexManager;
use wcf\system\tagging\TagEngine;
use wcf\system\user\activity\event\UserActivityEventHandler;
use wcf\system\user\activity\point\UserActivityPointHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;
use wcf\util\UserUtil;

/**
 * Executes news-related actions.
 *
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsAction extends AbstractDatabaseObjectAction implements IClipboardAction{
	protected $className = 'cms\data\news\NewsEditor';
	protected $permissionsDelete = array(
		'mod.cms.news.canModerateNews'
	);
	protected $allowGuestAccess = array(
		'getNewsPreview',
		'markAllAsRead'
	);
	public $news = null;

	public function create() {
		$data = $this->parameters['data'];
		// count attachments
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$data['attachments'] = count($this->parameters['attachmentHandler']);
		}
		if (LOG_IP_ADDRESS) {
			// add ip address
			if (! isset($data['ipAddress'])) {
				$data['ipAddress'] = WCF::getSession()->ipAddress;
			}
		}
		else {
			// do not track ip address
			if (isset($data['ipAddress'])) {
				unset($data['ipAddress']);
			}
		}

		$news = call_user_func(array(
			$this->className,
			'create'
		), $data);
		$newsEditor = new NewsEditor($news);

		// update attachments
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$this->parameters['attachmentHandler']->updateObjectID($news->newsID);
		}
		// tags
		if (! empty($this->parameters['tags'])) {
			TagEngine::getInstance()->addObjectTags('de.codequake.cms.news', $news->newsID, $this->parameters['tags'], $news->languageID);
		}
		// handle categories
		$newsEditor->updateCategoryIDs($this->parameters['categoryIDs']);
		$newsEditor->setCategoryIDs($this->parameters['categoryIDs']);

		// langID != 0
		$languageID = (! isset($this->parameters['data']['languageID']) || ($this->parameters['data']['languageID'] === null)) ? LanguageFactory::getInstance()->getDefaultLanguageID() : $this->parameters['data']['languageID'];
		$newsEditor->update(array(
			'languageID' => $languageID
		));

		if (! $news->isDisabled) {
			// recent
			if ($news->userID !== null && $news->userID != 0) {
				UserActivityEventHandler::getInstance()->fireEvent('de.codequake.cms.news.recentActivityEvent', $news->newsID, $news->languageID, $news->userID, $news->time);
				UserActivityPointHandler::getInstance()->fireEvent('de.codequake.cms.activityPointEvent.news', $news->newsID, $news->userID);
			}

			// update search index
			SearchIndexManager::getInstance()->add('de.codequake.cms.news', $news->newsID, $news->message, $news->subject, $news->time, $news->userID, $news->username, $news->languageID);

			// reset storage
			UserStorageHandler::getInstance()->resetAll('cmsUnreadNews');
		}
		return $news;
	}

	public function publish() {
		foreach ($this->objects as $news) {
			$news->update(array(
				'isDisabled' => 0
			));
			// recent
			UserActivityEventHandler::getInstance()->fireEvent('de.codequake.cms.news.recentActivityEvent', $news->newsID, $news->languageID, $news->userID, $news->time);
			UserActivityPointHandler::getInstance()->fireEvent('de.codequake.cms.activityPointEvent.news', $news->newsID, $news->userID);
			// update search index
			SearchIndexManager::getInstance()->add('de.codequake.cms.news', $news->newsID, $news->message, $news->subject, $news->time, $news->userID, $news->username, $news->languageID);
		}
		// reset storage
		UserStorageHandler::getInstance()->resetAll('cmsUnreadNews');
	}

	public function update() {
		// count attachments
		if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
			$this->parameters['data']['attachments'] = count($this->parameters['attachmentHandler']);
		}

		parent::update();

		$objectIDs = array();
		foreach ($this->objects as $news) {
			$objectIDs[] = $news->newsID;
		}
		if (! empty($objectIDs)) {
			SearchIndexManager::getInstance()->delete('de.codequake.cms.news', $objectIDs);
		}

		foreach ($this->objects as $news) {
			if (isset($this->parameters['categoryIDs'])) {
				$news->updateCategoryIDs($this->parameters['categoryIDs']);
			}

			// update tags
			$tags = array();
			if (isset($this->parameters['tags'])) {
				$tags = $this->parameters['tags'];
				unset($this->parameters['tags']);
			}
			if (! empty($tags)) {

				$languageID = (! isset($this->parameters['data']['languageID']) || ($this->parameters['data']['languageID'] === null)) ? LanguageFactory::getInstance()->getDefaultLanguageID() : $this->parameters['data']['languageID'];
				TagEngine::getInstance()->addObjectTags('de.codequake.cms.news', $news->newsID, $tags, $languageID);
			}
			// update search index
			SearchIndexManager::getInstance()->add('de.codequake.cms.news', $news->newsID, $news->message, $news->subject, $news->time, $news->userID, $news->username, $news->languageID);
		}
	}

	public function delete() {
		$newsIDs = array();
		$attachedNewsIDs = array();
		foreach ($this->objects as $news) {
			$newsIDs[] = $news->newsID;
			if ($news->attachments != 0) $attachedNewsIDs[] = $news->newsID;
		}
		// remove activity points
		UserActivityPointHandler::getInstance()->removeEvents('de.codequake.cms.activityPointEvent.news', $newsIDs);
		// remove attaches
		if (! empty($attachedNewsIDs)) {
			AttachmentHandler::removeAttachments('de.codequake.cms.news', $attachedNewsIDs);
		}
		// delete old search index entries
		if (! empty($objectIDs)) {
			SearchIndexManager::getInstance()->delete('de.codequake.cms.news', $newsIDs);
		}

		if (isset($this->parameters['unmarkItems'])) {
			$this->unmarkItems($newsIDs);
		}
		return parent::delete();
	}

	public function validateMarkAsRead() {
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}
	}

	public function markAsRead() {
		if (empty($this->parameters['visitTime'])) {
			$this->parameters['visitTime'] = TIME_NOW;
		}

		if (empty($this->objects)) {
			$this->readObjects();
		}

		$newsIDs = array();
		foreach ($this->objects as $news) {
			$newsIDs[] = $news->newsID;
			VisitTracker::getInstance()->trackObjectVisit('de.codequake.cms.news', $news->newsID, $this->parameters['visitTime']);
		}

		// reset storage
		if (WCF::getUser()->userID) {
			UserStorageHandler::getInstance()->reset(array(
				WCF::getUser()->userID
			), 'cmsUnreadNews');
		}
	}

	public function validateMarkAllAsRead() {
	/**
	 * Does nothing like a boss *
	 */
	}

	public function markAllAsRead() {
		VisitTracker::getInstance()->trackTypeVisit('de.codequake.cms.news');
		// reset storage
		if (WCF::getUser()->userID) {
			UserStorageHandler::getInstance()->reset(array(
				WCF::getUser()->userID
			), 'cmsUnreadNews');
		}
	}

	public function validateGetIpLog() {
		if (! LOG_IP_ADDRESS) {
			throw new PermissionDeniedException();
		}

		if (isset($this->parameters['newsID'])) {
			$this->news = new News($this->parameters['newsID']);
		}
		if ($this->news === null || ! $this->news->newsID) {
			throw new UserInputException('newsID');
		}

		if (! $this->news->canRead()) {
			throw new PermissionDeniedException();
		}
	}

	public function getIpLog() {
		// get ip addresses of the author
		$authorIpAddresses = News::getIpAddressByAuthor($this->news->userID, $this->news->username, $this->news->ipAddress);

		// resolve hostnames
		$newIpAddresses = array();
		foreach ($authorIpAddresses as $ipAddress) {
			$ipAddress = UserUtil::convertIPv6To4($ipAddress);

			$newIpAddresses[] = array(
				'hostname' => @gethostbyaddr($ipAddress),
				'ipAddress' => $ipAddress
			);
		}
		$authorIpAddresses = $newIpAddresses;

		// get other users of this ip address
		$otherUsers = array();
		if ($this->news->ipAddress) {
			$otherUsers = News::getAuthorByIpAddress($this->news->ipAddress, $this->news->userID, $this->news->username);
		}

		$ipAddress = UserUtil::convertIPv6To4($this->news->ipAddress);

		if ($this->news->userID) {
			$sql = "SELECT	registrationIpAddress
				FROM	wcf" . WCF_N . "_user
				WHERE	userID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$this->news->userID
			));
			$row = $statement->fetchArray();

			if ($row !== false && $row['registrationIpAddress']) {
				$registrationIpAddress = UserUtil::convertIPv6To4($row['registrationIpAddress']);
				WCF::getTPL()->assign(array(
					'registrationIpAddress' => array(
						'hostname' => @gethostbyaddr($registrationIpAddress),
						'ipAddress' => $registrationIpAddress
					)
				));
			}
		}

		WCF::getTPL()->assign(array(
			'authorIpAddresses' => $authorIpAddresses,
			'ipAddress' => array(
				'hostname' => @gethostbyaddr($ipAddress),
				'ipAddress' => $ipAddress
			),
			'otherUsers' => $otherUsers,
			'news' => $this->news
		));

		return array(
			'newsID' => $this->news->newsID,
			'template' => WCF::getTPL()->fetch('newsIpAddress', 'cms')
		);
	}

	public function getNewsPreview() {
		$list = new ViewableNewsList();
		$list->getConditionBuilder()->add("news.newsID = ?", array(
			$this->news->newsID
		));
		$list->readObjects();
		$news = $list->getObjects();
		WCF::getTPL()->assign(array(
			'news' => reset($news)
		));
		return array(
			'template' => WCF::getTPL()->fetch('newsPreview', 'cms')
		);
	}

	public function validateGetNewsPreview() {
		$this->news = $this->getSingleObject();
		// check if board may be entered and thread can be read
		foreach ($this->news->getCategories() as $category) {
			$category->getPermission('canViewNews');
		}
	}

	public function validateUnmarkAll() {
		// does nothing like a boss
	}

	public function unmarkAll() {
		ClipboardHandler::getInstance()->removeItems(ClipboardHandler::getInstance()->getObjectTypeID('de.codequake.cms.news'));
	}

	protected function unmarkItems(array $objectIDs = array()) {
		if (empty($objectIDs)) {
			foreach ($this->objects as $news) {
				$objectIDs[] = $news->newsID;
			}
		}

		if (!empty($objectIDs)) {
			ClipboardHandler::getInstance()->unmark($objectIDs, ClipboardHandler::getInstance()->getObjectTypeID('de.codequake.cms.news'));
		}
	}
}
