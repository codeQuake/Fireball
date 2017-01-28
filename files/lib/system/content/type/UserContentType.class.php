<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\exception\UserInputException;
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
	 * @inheritDoc
	 */
	protected $icon = 'fa-user';
	
	/**
	 * @inheritDoc
	 */
	protected $previewFields = ['name'];
	

	/**
	 * @inheritDoc
	 */
	public function validate(&$data) {
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
	 * @inheritDoc
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

		WCF::getTPL()->assign([
			'username' => $username
		]);

		return parent::getFormTemplate();
	}

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		if ($content->userID) {
			$user = UserProfileRuntimeCache::getInstance()->getObject($content->userID);
		}
		else if ($content->name) {
			$user = UserProfile::getUserProfileByUsername($content->name);
		}
		else {
			if (WCF::getUser()->hasAdministrativeAccess()) {
				return '<p class="error">Neither user id nor username provided</p>';
			} else {
				return '';
			}
		}

		if ($user === null) {
			if (WCF::getUser()->hasAdministrativeAccess()) {
				return '<p class="error">Please check content #' . $content->contentID . '. The specified user could not be found.</p>';
			}
			else {
				return '';
			}
		}

		WCF::getTPL()->assign([
			'user' => $user
		]);

		return parent::getOutput($content);
	}
}
