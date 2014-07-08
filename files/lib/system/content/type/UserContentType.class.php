<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\data\user\UserProfile;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\UserUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class UserContentType extends AbstractContentType {

	protected $icon = 'icon-user';

	public $objectType = 'de.codequake.cms.content.type.user';

	public function getFormTemplate() {
		return 'userContentType';
	}

	public function validate($data) {
		if (!isset($data['name']) || $data['name'] = '') throw new UserInputException('data[name]', 'empty');
		if (!UserProfile::getUserProfileByUsername($data['name'])) throw new UserInputException('data[name]', 'notValid');
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		WCF::getTPL()->assign(array(
			'user' => UserProfile::getUserProfileByUsername($data['name'])
		));
		return WCF::getTPL()->fetch('userContentTypeOutput', 'cms');
	}
}
