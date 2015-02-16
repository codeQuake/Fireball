<?php
namespace cms\system\poll;

use cms\data\content\Content;
use wcf\data\poll\Poll;
use wcf\system\poll\AbstractPollHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentPollHandler extends AbstractPollHandler {
	/**
	 * @see	\wcf\system\poll\IPollHandler::canStartPublicPoll()
	 */
	public function canStartPublicPoll() {
		return true;
	}

	/**
	 * @see	\wcf\system\poll\IPollHandler::canVote()
	 */
	public function canVote() {
		/**TODO**/
		return (WCF::getSession()->getPermission('user.cms.content.canVotePoll') ? true : false);
	}

	/**
	 * @see	\wcf\system\poll\IPollHandler::getRelatedObject()
	 */
	public function getRelatedObject(Poll $poll) {
		$content = new Content($poll->objectID);

		if ($content->contentID && $content->pollID == $poll->pollID) {
			return $content;
		}

		return null;
	}
}
