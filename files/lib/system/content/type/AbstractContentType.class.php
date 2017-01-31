<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Abstract content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
abstract class AbstractContentType implements IContentType {
	/**
	 * available content positions
	 * @var	string[]
	 */
	protected $availablePositions = ['hero', 'headerBoxes', 'top', 'sidebarLeft', 'body', 'sidebarRight', 'bottom', 'footerBoxes', 'footer'];

	/**
	 * name of the icon to display
	 * @var	string
	 */
	protected $icon = 'fa-unchecked';

	/**
	 * list of multilingual fields
	 * @var	array<string>
	 */
	public $multilingualFields = [];

	/**
	 * list of preview fields
	 * @var array<string>
	 */
	protected $previewFields = [];
	
	/**
	 * content requires title
	 * @var boolean
	 */
	public $requiresTitle = false;
	
	/**
	 * template name
	 * @var	string
	 */
	public $templateName = '';

	/**
	 * content type supports inline editing
	 * @var boolean
	 */
	public $inlineEditingEnabled = false;

	/**
	 * template name for inline editing
	 * @var	string
	 */
	public $inlineTemplateName = '';

	/**
	 * Initialize a new content type instance
	 */
	public function __construct() {
		// try to guess template name
		if (empty($this->templateName)) {
			$classParts = explode('\\', get_class($this));
			$className = array_pop($classParts);
			$this->templateName = lcfirst($className);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		WCF::getTPL()->assign([
			'content' => $content
		]);

		return WCF::getTPL()->fetch($this->templateName, 'cms');
	}

	/**
	 * @inheritDoc
	 */
	public function getIcon() {
		return $this->icon;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getPreview(Content $content) {
		if (!empty($this->previewFields)) {
			$preview = '';
			foreach ($this->previewFields as $field) {
				if ((string) $content->{$field} != '') {
					$preview .= ' - ';
					$preview .= strip_tags($content->{$field});
				}
			}
			return StringUtil::truncate(substr($preview, 3), 70);
		}
		else {
			//no fields given, return ID
			return 'Content #'.$content->contentID;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function isAvailableToAdd($position) {
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		// register multilingual fields
		foreach ($this->multilingualFields as $field) {
			I18nHandler::getInstance()->register($field);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() { /* nothing */ }

	/**
	 * @inheritDoc
	 */
	public function validate(&$data) { /* nothing */ }

	/**
	 * @inheritDoc
	 */
	public function getFormTemplate() {
		return $this->templateName;
	}

	/**
	 * @inheritdoc
	 */
	public function getInlineFormTemplate() {
		return $this->inlineTemplateName;
	}

	/**
	 * @inheritDoc
	 */
	public function getSortableOutput(Content $content) {
		return $this->getOutput($content);
	}
}
