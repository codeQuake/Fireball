<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\data\user\UserProfile;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class UserContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-user';

	public function getFormTemplate() {
		return 'userContentType';
	}

	public function validate($data) {
		if (!isset($data['name']) || $data['name'] == '') throw new UserInputException('data[name]', 'empty');
		if (!UserProfile::getUserProfileByUsername($data['name'])) throw new UserInputException('data[name]', 'notValid');
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		WCF::getTPL()->assign(array(
			'user' => UserProfile::getUserProfileByUsername($data['name'])
		));
		return WCF::getTPL()->fetch('userContentType', 'cms');
	}
}
