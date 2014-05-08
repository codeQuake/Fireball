<?php
namespace cms\system\content\type;

/**
 * @author	Jens Krumsieck
 * @copyright	codeQuake 2014
 * @package	de.codequake.cms
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 *
 */
class HeadlineContentType extends AbstractContentType{
	protected $icon  = 'icon-underline';

	public $objectType = 'de.codequake.cms.content.type.headline';

	public function getFormTemplate(){
		return 'headlineContentType';
	}

}