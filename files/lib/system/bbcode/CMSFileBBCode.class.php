<?php
namespace cms\system\bbcode;

use cms\data\file\File;
use wcf\system\bbcode\AbstractBBCode;
use wcf\system\bbcode\BBCodeParser;
use wcf\system\WCF;

/**
 * handles the cmsFile bbcode
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSFileBBCode extends AbstractBBCode {

	public $isImage = false;
	public $align = '';
	public $width = 0;
	public $caption = '';

	public function getParsedTag(array $openingTag, $content, array $closingTag, BBCodeParser $parser) {
		//get id attribute
		$fileID = 0;
		if (isset($openingTag['attributes'][0])) {
			$fileID = $openingTag['attributes'][0];
		}
		$file = new File($fileID);

		if ($file === null) return '';

		if (preg_match('~(image/*)+~', $file->type)) $this->isImage = true;

		if ($this->isImage && isset($openingTag['attributes'][1])) {
			$this->align = $openingTag['attributes'][1];
		}

		if ($this->isImage && isset($openingTag['attributes'][2])) {
			$this->width = $openingTag['attributes'][2];
		}

		if ($this->isImage && isset($openingTag['attributes'][3])) {
			$this->caption = $openingTag['attributes'][3];
		}

		WCF::getTPL()->assign(array(
			'_file' => $file,
			'_align' => $this->align,
			'_width' => $this->width,
			'_isImage' => $this->isImage,
			'_caption' => $this->caption

		));

		return WCF::getTPL()->fetch('cmsFileBBCodeTag', 'cms');
	}
}
