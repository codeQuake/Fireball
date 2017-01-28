<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\data\content\ContentCache;
use wcf\system\exception\UserInputException;
use wcf\util\ArrayUtil;

/**
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ColumnsContentType extends AbstractStructureContentType {
	/**
	 * @inheritDoc
	 */
	public function getCSSClasses() {
		return 'gridContainer';
	}

	/**
	 * @inheritDoc
	 */
	public function getChildCSSClasses(Content $content) {
		$parent = $content->getParentContent();

		$columnData = $parent->columnData;
		$columnCount = count($columnData);

		$siblingIDs = ContentCache::getInstance()->getChildIDs($parent->contentID);
		$siblingNumber = array_search($content->contentID, $siblingIDs);

		if ($columnCount > 0)
			$width = $columnData[$siblingNumber % $columnCount];
		else
			$width = $columnData[$siblingNumber];
		return 'grid grid'.$width;
	}

	/**
	 * @inheritDoc
	 */
	public function validate(&$data) {
		$accumulatedColumnWidth = 0;

		if (!isset($data['columnData']) || !is_array($data['columnData'])) {
			throw new UserInputException('columnData');
		}

		$data['columnData'] = ArrayUtil::toIntegerArray($data['columnData']);
		$columnCount = count($data['columnData']);

		$minColumnCount = 2;
		$maxColumnCount = 5;
		$minColumnWidth = 20;

		if ($columnCount < $minColumnCount || $columnCount > $maxColumnCount) {
			throw new UserInputException('columnData');
		}

		foreach ($data['columnData'] as $column => $width) {
			if ($width < $minColumnWidth) {
				throw new UserInputException('columnData');
			}

			$accumulatedColumnWidth += $width;
		}

		if ($accumulatedColumnWidth !== 100) {
			throw new UserInputException('columnData');
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		return '';
	}
}
