<?php
namespace cms\system\poll;
use cms\data\content\Content;
use wcf\data\poll\Poll;
use wcf\system\poll\AbstractPollHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class ContentPollHandler extends AbstractPollHandler {

	public function canStartPublicPoll() {
		return true;
	}

	public function canVote() {
		/**TODO**/
		return (WCF::getSession()->getPermission('user.cms.content.canVotePoll') ? true : false);
	}

	public function getRelatedObject(Poll $poll) {
		$content = new Content($poll->objectID);
		$data = $content->handleContentData();
		if ($content->contentID && $data['pollID'] == $poll->pollID) {
			return $content;
		}

		return null;
	}
}
