<?php
namespace cms\system\option;

use cms\data\page\PageCache;
use cms\data\page\PageNodeTree;
use wcf\data\option\Option;
use wcf\system\exception\UserInputException;
use wcf\system\option\AbstractOptionType;
use wcf\system\WCF;

/**
 * Option type implementation for cms page selection.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSPageSelectOptionType extends AbstractOptionType {
	/**
	 * @see	\wcf\system\option\IOptionType::getData()
	 */
	public function getData(Option $option, $newValue) {
		return intval($newValue);
	}

	/**
	 * @see	\wcf\system\option\IOptionType::getFormElement()
	 */
	public function getFormElement(Option $option, $value) {
		$nodeTree = new PageNodeTree();
		$nodeList = $nodeTree->getIterator();

		WCF::getTPL()->assign(array(
			'nodeList' => $nodeList,
			'option' => $option,
			'value' => $value
		));

		return WCF::getTPL()->fetch('pageSelectOptionType', 'cms');
	}

	/**
	 * @see	\wcf\system\option\IOptionType::validate()
	 */
	public function validate(Option $option, $newValue) {
		if (!empty($newValue)) {
			if (PageCache::getInstance()->getPage($newValue) === null) {
				throw new UserInputException($option->optionName, 'validationFailed');
			}
		}
	}
}
