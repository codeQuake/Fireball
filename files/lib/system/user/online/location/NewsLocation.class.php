<?php
namespace cms\system\user\online\location;

use cms\data\news\News;
use wcf\data\user\online\UserOnline;
use wcf\system\user\online\location\IUserOnlineLocation;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsLocation implements IUserOnlineLocation {

	public function cache(UserOnline $user) {}

	public function get(UserOnline $user, $languageVariable = '') {
		$news = new News($user->objectID);
		if ($news->newsID != 0) {
			if ($news->isVisible()) {
				return WCF::getLanguage()->getDynamicVariable($languageVariable, array(
					'news' => $news
				));
			}
		}
		return '';
	}
}
