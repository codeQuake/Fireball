<?php
namespace cms\data\news;

use wcf\data\IFeedEntry;
use wcf\system\request\LinkHandler;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsFeed extends ViewableNews implements IFeedEntry {

	public function getTitle() {
		return $this->getDecoratedObject()->getTitle();
	}

	public function getLink() {
		return LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'object' => $this->getDecoratedObject(),
			'appendSession' => false,
			'encodeTitle' => true
		));
	}

	public function getFormattedMessage() {
		return $this->getDecoratedObject()->getFormattedMessage();
	}

	public function getMessage() {
		return $this->getDecoratedObject()->getMessage();
	}

	public function getComments() {
		return $this->comments;
	}

	public function getExcerpt($maxLength = CMS_NEWS_PREVIEW_TRUNCATE) {
		return $this->getDecoratedObject()->getExcerpt($maxLength);
	}

	public function getTime() {
		return $this->time;
	}

	public function getUserID() {
		return $this->userID;
	}

	public function getUsername() {
		return $this->username;
	}

	public function __toString() {
		return $this->getDecoratedObject()->__toString();
	}

	public function isVisible() {
		return $this->canRead();
	}

	public function getCategories() {
		$categoryNames = array();
		foreach ($this->getDecoratedObject()->getCategories() as $category) {
			$categoryNames[] = $category->getTitle();
		}
		
		return $categoryNames;
	}
}
