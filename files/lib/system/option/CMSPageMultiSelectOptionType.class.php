<?php
namespace cms\system\option;

use cms\data\page\PageCache;
use cms\data\page\PageNodeTree;
use wcf\data\option\Option;
use wcf\system\exception\UserInputException;
use wcf\system\option\AbstractOptionType;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Option type implementation for cms page multi select lists.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSPageMultiSelectOptionType extends AbstractOptionType {
	/**
	 * @see	\wcf\system\option\IOptionType::getData()
	 */
	public function getData(Option $option, $newValue) {
		if (!is_array($newValue)) {
			$newValue = [];
		}

		return implode("\n", ArrayUtil::toIntegerArray($newValue));
	}

	/**
	 * @see	\wcf\system\option\IOptionType::getFormElement()
	 */
	public function getFormElement(Option $option, $value) {
		$nodeTree = new PageNodeTree();
		$nodeList = $nodeTree->getIterator();

		WCF::getTPL()->assign([
			'nodeList' => $nodeList,
			'option' => $option,
			'value' => (!is_array($value) ? explode("\n", $value) : $value)
		]);

		return WCF::getTPL()->fetch('pageMultiSelectOptionType', 'cms');
	}

	/**
	 * @see	\wcf\system\option\IOptionType::validate()
	 */
	public function validate(Option $option, $newValue) {
		if (!is_array($newValue)) $newValue = [];
		$newValue = ArrayUtil::toIntegerArray($newValue);

		foreach ($newValue as $pageID) {
			if (PageCache::getInstance()->getPage($pageID) === null) {
				throw new UserInputException($option->optionName, 'validationFailed');
			}
		}
	}
}
