<?php
namespace cms\system\content\type;

use cms\data\content\Content;

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

	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		$url = $data['video'];
		parse_str(parse_url($url, PHP_URL_QUERY), $var);
		$videoID = $var['v'];

		switch ($data['size']) {
			case 1:
				$width = 560;
				$height = 315;
				break;
			case 2:
				$width = 640;
				$height = 360;
				break;
			case 3:
				$width = 853;
				$height = 480;
				break;
			case 4:
				$width = 1280;
				$height = 720;
				break;
			case 5:
				$width = 1920;
				$height = 1080;
				break;
			default:
				$width = 640;
				$height = 360;
				break;
		}

		return '<iframe width="'.$width.'" height="'.$height.'" src="http://youtube.com/embed/'.$videoID.'" frameborder="0" allowfullscreen></iframe>';
	}
}
