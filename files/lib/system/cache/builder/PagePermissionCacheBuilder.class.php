<?php
namespace cms\system\cache\builder;

use cms\data\page\Page;
use cms\data\page\PageList;
use wcf\system\acl\ACLHandler;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PagePermissionCacheBuilder extends AbstractCacheBuilder {
	public function rebuild(array $parameters) {
		$data = array();
		$objectTypeName = 'de.codequake.cms.page';
		$pageList = new PageList();
		$pageList->readObjects();
		$pageList = $pageList->getObjects();
		
		$aclOptions = ACLHandler::getInstance()->getPermissions(ACLHandler::getInstance()->getObjectTypeID($objectTypeName), array_keys($pageList));
		$options = $aclOptions['options']->getObjects();
		foreach (array(
			'group',
			'user'
		) as $type) {
			foreach ($aclOptions[$type] as $pageID => $optionData) {
				if (! isset($data[$pageID])) {
					$data[$pageID] = array(
						'group' => array(),
						'user' => array()
					);
				}
				foreach ($optionData as $typeID => $optionValues) {
					$data[$pageID][$type][$typeID] = array();
					
					foreach ($optionValues as $optionID => $optionValue) {
						$data[$pageID][$type][$typeID][$options[$optionID]->optionName] = $optionValue;
					}
				}
			}
		}
		return $data;
	}
}
