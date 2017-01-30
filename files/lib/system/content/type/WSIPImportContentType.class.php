<?php

namespace cms\system\content\type;
use cms\data\content\Content;
use cms\data\content\ContentAction;
use cms\data\file\FileCache;
use cms\data\page\PageCache;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\request\RequestHandler;
use wcf\system\WCF;

/**
 * @author	Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class WSIPImportContentType extends TemplateContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-code';
	
	/**
	 * @inheritDoc
	 */
	protected $previewFields = ['text'];
	
	/**
	 * @inheritDoc
	 */
	public $templateName = 'wsipImportContentType';

	/**
	 * @inheritDoc
	 */
	public function validate(&$data) {
		if (!isset($data['text']) || empty($data['text'])) {
			throw new UserInputException('text');
		}
		
		// check template code
		try {
			$compiled = WCF::getTPL()->getCompiler()->compileString('de.codequake.cms.content.type.template', $data['text'], [], true);
			
			// cache compiled template with content
			RequestHandler::getInstance()->getActiveRequest()->getRequestObject()->contentData['compiled'][WCF::getLanguage()->languageCode] = $compiled;
		}
		catch (SystemException $e) {
			WCF::getTPL()->assign([
				'compileError' => $e->_getMessage()
			]);

			throw new UserInputException('text', 'compileError');
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		$compiled = $content->compiled;
		if (empty($compiled[WCF::getLanguage()->languageCode])) {
			$source = $content->text;
			$source = preg_replace_callback('/\[fireball\=([0-9]+)\](.*)\[\/fireball\]/', function ($match) {
				$page = PageCache::getInstance()->getPage($match[1]);
				
				if ($page === null)
					return '(page not found)';
				
				$title = empty($match[2]) ? $page->getTitle() : $match[2];
				return '<a href="' . $page->getLink() . '" class="pagePreview">' . $title . '</a>';
			}, $source);
			$source = preg_replace_callback("/\[cmsfile\=([0-9]+)( ?, ?(left|right|none))?( ?, ?([0-9]+))?( ?, ?'(.*)')?\](.*)\[\/cmsfile\]/", function ($match) {
				$file = FileCache::getInstance()->getFile($match[1]);
				if ($file === null) return '';
				
				$align = !empty($match[3]) ? $match[3] : '';
				$width = !empty($match[5]) ? $match[5] : 0;
				$caption = !empty($match[7]) ? $match[7] : '';
				
				return WCF::getTPL()->fetch('cmsFileBBCodeTag', 'cms', [
					'_file' => $file,
					'_align' => $align,
					'_width' => $width,
					'_isImage' => preg_match('~(image/*)+~', $file->fileType),
					'_caption' => $caption
				]);
			}, $source);
			$compiled = WCF::getTPL()->getCompiler()->compileString('de.codequake.cms.content.type.template' . $content->contentID, $source);
			
			$contentData = $content->contentData;
			if (!is_array($contentData))
				$contentData = unserialize($contentData);
			$contentData['compiled'][WCF::getLanguage()->languageCode] = $compiled;
			
			$contentAction = new ContentAction([$content], 'update', ['data' => ['contentData' => $contentData]]);
			$contentAction->executeAction();
		} else {
			$compiled = $content->compiled[WCF::getLanguage()->languageCode];
		}
		
		return WCF::getTPL()->fetchString($compiled['template']);
	}
	
	/**
	 * @inheritDoc
	 */
	public function isAvailableToAdd() {
		// only available for imported contents
		return false;
	}
}
