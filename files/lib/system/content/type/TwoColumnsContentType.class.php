<?php
namespace cms\system\content\type;
use cms\data\content\Content;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 *
 */
class TwoColumnsContentType extends AbstractContentType{
	protected $icon = 'icon-columns';
	public $objectType = 'de.codequake.cms.content.type.twocolumns';

	public function getFormTemplate() {
		return 'columnContentType';
	}

	public function getOutput(Content $content){
		return '';
	}
}
