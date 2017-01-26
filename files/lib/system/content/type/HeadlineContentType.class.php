<?php
namespace cms\system\content\type;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class HeadlineContentType extends AbstractSearchableContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-underline';

	/**
	 * @inheritDoc
	 */
	public $multilingualFields = ['text'];

	/**
	 * @inheritDoc
	 */
	protected $searchableFields = ['text'];
}
