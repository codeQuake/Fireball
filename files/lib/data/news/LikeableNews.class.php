<?php
namespace cms\data\news;
use wcf\data\like\object\AbstractLikeObject;
use wcf\system\request\LinkHandler;


class LikeableNews extends AbstractLikeObject {

	protected static $baseClass = 'cms\data\news\News';

	public function getTitle() {
		return $this->subject;
	}

	public function getURL() {
		return LinkHandler::getInstance()->getLink('News', array(
			'application' => 'cms',
			'object' => $this->getDecoratedObject()
		));
	}

	public function getUserID() {
		return $this->userID;
	}

	public function getObjectID() {
		return $this->newsID;
	}

	public function updateLikeCounter($cumulativeLikes) {
		// update cumulative likes
		$editor = new NewsEditor($this->getDecoratedObject());
		$editor->update(array(
			'cumulativeLikes' => $cumulativeLikes
		));
	}
}
