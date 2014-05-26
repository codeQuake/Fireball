<?php
namespace cms\system\poll;
use cms\data\news\News;
use wcf\data\poll\Poll;
use wcf\system\poll\AbstractPollHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class NewsPollHandler extends AbstractPollHandler {

	public function canStartPublicPoll() {
		return (WCF::getSession()->getPermission('user.cms.news.canStartPublicPoll') ? true : false);
	}

	public function canVote() {
		return (WCF::getSession()->getPermission('user.cms.news.canVotePoll') ? true : false);
	}

	public function getRelatedObject(Poll $poll) {
		$news = new News($poll->objectID);
		if ($news->newsID && $news->pollID == $poll->pollID) {
			return $news;
		}

		return null;
	}
}
