<?php
namespace cms\system\content\type;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class LinkContentType extends AbstractSearchableContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-link';

	/**
	 * @inheritDoc
	 */
	public $multilingualFields = ['text', 'link'];
	
	/**
	 * @inheritDoc
	 */
	protected $searchableFields = ['text'];
}
