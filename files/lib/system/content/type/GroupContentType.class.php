<?php
namespace cms\system\content\type;

use cms\data\content\Content;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class GroupContentType extends AbstractStructureContentType {
	/**
	 * @inheritDoc
	 */
	public function getCSSClasses() {
		return 'contentCollection';
	}
	
	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		return '';
	}
}
