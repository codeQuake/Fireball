<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\exception\UserInputException;
use wcf\util\FileUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
class YoutubeContentType extends AbstractContentType {

	protected $icon = 'icon-youtube';

	public $objectType = 'de.codequake.cms.content.type.youtube';

	public function getFormTemplate() {
		return 'youtubeContentType';
	}

	public function validate($data) {
		if (!isset($data['video'])) throw new UserInputException('data[video]', 'empty');
		if (!FileUtil::isURL($data['video'])) throw new UserInputException('data[video]', 'notValid');
	}

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		$url = $data['video'];
		parse_str(parse_url($url, PHP_URL_QUERY), $var);
		if (isset($var['v'])) {
			$videoID = $var['v'];
			return '<div class="elastic_video"><iframe width="640" height="360" src="http://youtube.com/embed/' . $videoID . '" frameborder="0" allowfullscreen></iframe></div>';
		}
		return '';
	}
}
