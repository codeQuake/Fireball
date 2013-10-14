<?php
namespace cms\system\cache\builder;
use wcf\system\acl\ACLHandler;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

class PagePermissionCacheBuilder extends AbstractCacheBuilder {
    
     function rebuild(array $parameters) {
		$data = array();
		
		if (!empty($parameters)) {
			$conditionBuilder = new PreparedStatementConditionBuilder();
			$conditionBuilder->add('acl_option.objectTypeID = ?', array(ACLHandler::getInstance()->getObjectTypeID('de.codequake.cms.page')));
			$conditionBuilder->add('acl_option.categoryName LIKE ?', array('user.%'));
			$conditionBuilder->add('option_to_group.optionID = acl_option.optionID');
			$conditionBuilder->add('option_to_group.groupID IN (?)', array($parameters));
			$sql = "SELECT		option_to_group.groupID, option_to_group.objectID AS pageID, option_to_group.optionValue,
						acl_option.optionName AS permission
				FROM		wcf".WCF_N."_acl_option acl_option,
						wcf".WCF_N."_acl_option_to_group option_to_group
						".$conditionBuilder;
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($conditionBuilder->getParameters());
			while ($row = $statement->fetchArray()) {
				if (!isset($data[$row['pageID']][$row['permission']])) $data[$row['pageID']][$row['permission']] = $row['optionValue'];
				else $data[$row['pageID']][$row['permission']] = $row['optionValue'] || $data[$row['pageID']][$row['permission']];
			}
			
		}
		
		return $data;
	}
}
