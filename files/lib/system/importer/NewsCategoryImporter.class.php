<?php
namespace cms\system\importer;

use wcf\data\object\type\ObjectTypeCache;
use wcf\system\importer\AbstractCategoryImporter;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsCategoryImporter extends AbstractCategoryImporter {
	protected $objectTypeName = 'de.codequake.cms.category.news';

	public function __construct() {
		$objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('com.woltlab.wcf.category', 'de.codequake.cms.category.news');
		$this->objectTypeID = $objectType->objectTypeID;
	}
}
