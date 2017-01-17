<?php
namespace cms\system\cache\builder;

use cms\data\file\FileList;
use wcf\system\acl\ACLHandler;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches file permissions.
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FilePermissionCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		$data = [];
		$objectTypeName = 'de.codequake.cms.file';
		$fileList = new FileList();
		$fileList->readObjects();
		$fileList = $fileList->getObjects();

		$aclOptions = ACLHandler::getInstance()->getPermissions(ACLHandler::getInstance()->getObjectTypeID($objectTypeName), array_keys($fileList));
		$options = $aclOptions['options']->getObjects();
		foreach ([
			'group',
			'user'
		] as $type) {
			foreach ($aclOptions[$type] as $fileID => $optionData) {
				if (!isset($data[$fileID])) {
					$data[$fileID] = [
						'group' => [],
						'user' => []
					];
				}
				foreach ($optionData as $typeID => $optionValues) {
					$data[$fileID][$type][$typeID] = [];
					
					foreach ($optionValues as $optionID => $optionValue) {
						$data[$fileID][$type][$typeID][$options[$optionID]->optionName] = $optionValue;
					}
				}
			}
		}
		return $data;
	}
}
