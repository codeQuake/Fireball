<?php
namespace cms\system\cache\builder;

use cms\data\content\ContentList;
use wcf\system\acl\ACLHandler;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches content permissions.
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentPermissionCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		$data = array();
		$objectTypeName = 'de.codequake.cms.content';
		$contentList = new ContentList();
		$contentList->readObjects();
		$contentList = $contentList->getObjects();

		$aclOptions = ACLHandler::getInstance()->getPermissions(ACLHandler::getInstance()->getObjectTypeID($objectTypeName), array_keys($contentList));
		$options = $aclOptions['options']->getObjects();
		foreach (array(
			'group',
			'user'
		) as $type) {
			foreach ($aclOptions[$type] as $contentID => $optionData) {
				if (!isset($data[$contentID])) {
					$data[$contentID] = array(
						'group' => array(),
						'user' => array()
					);
				}
				foreach ($optionData as $typeID => $optionValues) {
					$data[$contentID][$type][$typeID] = array();
					
					foreach ($optionValues as $optionID => $optionValue) {
						$data[$contentID][$type][$typeID][$options[$optionID]->optionName] = $optionValue;
					}
				}
			}
		}
		return $data;
	}
}
