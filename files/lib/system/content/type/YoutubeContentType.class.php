<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\exception\UserInputException;
use wcf\util\FileUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class YoutubeContentType extends AbstractContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-youtube';
	
	/**
	 * @inheritDoc
	 */
	protected $previewFields = ['video'];

	/**
	 * @inheritDoc
	 */
	public function validate($data) {
		if (!isset($data['video'])) {
			throw new UserInputException('data[video]');
		}

		if (!FileUtil::isURL($data['video'])) {
			throw new UserInputException('data[video]', 'notValid');
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		parse_str(parse_url($content->video, PHP_URL_QUERY), $var);

		if (isset($var['v'])) {
			$videoID = $var['v'];
			return '<div class="elastic_video"><iframe width="640" height="360" src="https://youtube.com/embed/' . $videoID . '" frameborder="0" allowfullscreen></iframe></div>';
		}
		return '';
	}
}
