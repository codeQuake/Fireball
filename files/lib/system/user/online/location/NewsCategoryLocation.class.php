<?php
namespace cms\system\user\online\location;

use cms\data\category\NewsCategory;
use wcf\data\user\online\UserOnline;
use wcf\system\category\CategoryHandler;
use wcf\system\user\online\location\IUserOnlineLocation;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCategoryLocation implements IUserOnlineLocation {

	public function cache(UserOnline $user) {}

	public function get(UserOnline $user, $languageVariable = '') {
		if ($category = CategoryHandler::getInstance()->getCategory($user->objectID)) {
			$category = new NewsCategory($category);
			if ($category->getPermission('canView')) {
				return WCF::getLanguage()->getDynamicVariable($languageVariable, array(
					'category' => $category
				));
			}
		}
		return '';
	}
}
