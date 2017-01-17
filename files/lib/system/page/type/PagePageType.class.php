<?php

namespace cms\system\page\type;
use cms\data\page\Page;
use cms\page\PagePage;
use cms\system\page\type\AbstractPageType;
use wcf\form\AbstractForm;

/**
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PagePageType extends AbstractPageType {
	/**
	 * @inheritdoc
	 */
	public $frontendController = PagePage::class;

	/**
	 * @see  \cms\system\page\type\AbstractPageType::$assignValues
	 */
	public $assignValues = [
		'isCommentable' => FIREBALL_PAGES_DEFAULT_COMMENTS
	];
	
	/**
	 * @see \cms\system\page\type\AbstractPageType::readFormParameters()
	 */
	public function readFormParameters(AbstractForm $form) {
		$formParameters = parent::readFormParameters($form);
		
		$this->assignValues['isCommentable'] = isset($_POST['isCommentable']) ? 1 : 0;
		
		return array_merge($formParameters, $this->assignValues);
	}
	
	/**
	 * @see \cms\system\page\type\AbstractPageType::readData()
	 */
	public function readData(AbstractForm $form) {
		$return = parent::readData($form);
		
		if (empty($_POST)) {
			if (!empty($form->page->isCommentable)) $return['isCommentable'] = $form->page->isCommentable;
		}
		
		return $return;
	}
}
