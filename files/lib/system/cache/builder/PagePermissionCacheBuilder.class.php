<?php
namespace cms\system\cache\builder;

use cms\data\page\PageList;
use wcf\system\acl\ACLHandler;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches page permissions.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PagePermissionCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	public function rebuild(array $parameters) {
		$data = [];
		$objectTypeName = 'de.codequake.cms.page';
		$pageList = new PageList();
		$pageList->readObjects();
		$pageList = $pageList->getObjects();

		$aclOptions = ACLHandler::getInstance()->getPermissions(ACLHandler::getInstance()->getObjectTypeID($objectTypeName), array_keys($pageList));
		$options = $aclOptions['options']->getObjects();
		foreach ([
			'group',
			'user'
		] as $type) {
			foreach ($aclOptions[$type] as $pageID => $optionData) {
				if (!isset($data[$pageID])) {
					$data[$pageID] = [
						'group' => [],
						'user' => []
					];
				}
				foreach ($optionData as $typeID => $optionValues) {
					$data[$pageID][$type][$typeID] = [];
					
					foreach ($optionValues as $optionID => $optionValue) {
						$data[$pageID][$type][$typeID][$options[$optionID]->optionName] = $optionValue;
					}
				}
			}
		}
		return $data;
	}
}
