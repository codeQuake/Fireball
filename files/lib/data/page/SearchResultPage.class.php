<?php
namespace cms\data\page;

use wcf\data\search\ISearchResultObject;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\request\LinkHandler;
use wcf\system\search\SearchResultTextParser;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class SearchResultPage extends ViewablePage implements ISearchResultObject {
	/**
	 * @var UserProfile
	 */
	protected $userProfile = null;
	
	/**
	 * @inheritDoc
	 */
	public function getFormattedMessage() {
		return SearchResultTextParser::getInstance()->parse(WCF::getLanguage()->get($this->description));
	}
	
	/**
	 * @inheritDoc
	 */
	public function getLink($query = '') {
		if ($query) {
			return LinkHandler::getInstance()->getLink('Page', [
				'alias' => $this->getDecoratedObject()->getAlias(),
				'application' => 'cms',
				'highlight' => urlencode($query)
			]);
		}

		return $this->getDecoratedObject()->getLink();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getSubject() {
		return WCF::getLanguage()->get($this->title);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getUserProfile() {
		if ($this->userProfile === null) {
			$this->userProfile = new UserProfile(new User($this->authorID));
		}

		return $this->userProfile;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTime() {
		return $this->creationTime;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getObjectTypeName() {
		return 'de.codequake.cms.page';
	}
	
	/**
	 * @inheritDoc
	 */
	public function getContainerLink() {
		return '';
	}
	
	/**
	 * @inheritDoc
	 */
	public function getContainerTitle() {
		return '';
	}
}
