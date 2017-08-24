<?php

namespace cms\system\page\type;
use cms\page\PagePage;
use wcf\form\AbstractForm;

/**
 * @author	Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PagePageType extends AbstractPageType {
	/**
	 * @inheritdoc
	 */
	public $frontendController = PagePage::class;

	/**
	 * @inheritDoc
	 */
	public $assignValues = [
		'isCommentable' => FIREBALL_PAGES_DEFAULT_COMMENTS
	];
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters(AbstractForm $form) {
		$formParameters = parent::readFormParameters($form);
		
		$this->assignValues['isCommentable'] = isset($_POST['isCommentable']) ? 1 : 0;
		
		return array_merge($formParameters, $this->assignValues);
	}
	
	/**
	 * @inheritDoc
	 */
	public function readData(AbstractForm $form) {
		$return = parent::readData($form);
		
		if (empty($_POST)) {
			if (!empty($form->page->isCommentable)) $return['isCommentable'] = $form->page->isCommentable;
		}
		
		return $return;
	}
}
