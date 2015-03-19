<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\exception\UserInputException;
use wcf\system\exception\SystemException;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class UserContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-user';

	/**
	 * @see	\cms\system\content\type\IContentType::validate()
	 */
	public function validate($data) {
		if (!isset($data['name']) || $data['name'] == '') {
			throw new UserInputException('data[name]');
		}

		$userProfile = User::getUserByUsername($data['name']);

		if (!$userProfile) {
			throw new UserInputException('data[name]', 'notValid');
		}

		// save user id instead of username
		$contentData = &RequestHandler::getInstance()->getActiveRequest()->getRequestObject()->contentData;
		$contentData['userID'] = $userProfile->userID;
		unset($contentData['name']);
	}

	/**
	 * @see \cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		$username = '';
		$contentData = &RequestHandler::getInstance()->getActiveRequest()->getRequestObject()->contentData;

		if (isset($contentData['name'])) {
			$username = $contentData['name'];
		}
		else if (isset($contentData['userID'])) {
			$userID = $contentData['userID'];
			$user = new User($userID);

			if ($user->userID) {
				$username = $user->username;
			}
		}

		WCF::getTPL()->assign(array(
			'username' => $username
		));

		return parent::getFormTemplate();
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		if ($content->userID) {
			$user = UserProfile::getUserProfile($content->userID);
		}
		else if ($content->name) {
			$user = UserProfile::getUserProfileByUsername($content->name);
		}
		else {
			throw new SystemException('Neither user id nor username provided');
		}

		WCF::getTPL()->assign(array(
			'user' => $user
		));

		return parent::getOutput($content);
	}
}
