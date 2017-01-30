<?php
namespace cms\system\poll;

use cms\data\content\Content;
use wcf\data\poll\Poll;
use wcf\system\poll\AbstractPollHandler;
use wcf\system\WCF;

/**
 * Poll implementation for the poll content type.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentPollHandler extends AbstractPollHandler {
	/**
	 * @inheritDoc
	 * @todo	only admins with the right to create contents are
	 * 		allowed to start a public poll.
	 */
	public function canStartPublicPoll() {
		return true;
	}

	/**
	 * @inheritDoc
	 * @todo	only users that can view the content are allowed to
	 * 		vote.
	 */
	public function canVote() {
		return (WCF::getSession()->getPermission('user.fireball.content.canVotePoll') ? true : false);
	}

	/**
	 * @inheritDoc
	 */
	public function getRelatedObject(Poll $poll) {
		$content = new Content($poll->objectID);

		if ($content->contentID && $content->pollID == $poll->pollID) {
			return $content;
		}

		return null;
	}
}
