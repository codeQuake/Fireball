<?php

namespace cms\system\bbcode;
use cms\data\file\File;
use cms\data\file\FileCache;
use wcf\system\bbcode\AbstractBBCode;
use wcf\system\bbcode\BBCodeParser;

/**
 * Handles the output for the cms file internal bbcode.
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSFileUrlBBCode extends AbstractBBCode {
	public $fileID = 0;
	public $file = null;

	public function getParsedTag(array $openingTag, $content, array $closingTag, BBCodeParser $parser) {
		$this->fileID = $content;
		$this->file = FileCache::getInstance()->getFile($this->fileID);
		if ($this->file === null)
			$this->file = new File($this->fileID);
		
		if ($this->file == null)
			return '';
		
		return $this->file->getLink();
	}
}
