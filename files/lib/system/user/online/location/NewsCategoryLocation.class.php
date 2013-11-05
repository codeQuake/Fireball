<?php
namespace cms\system\user\online\location;
use wcf\data\user\online\UserOnline;
use wcf\system\category\CategoryHandler;
use cms\data\category\NewsCategory;
use wcf\system\user\online\location\IUserOnlineLocation;
use wcf\system\WCF;

class NewsCategoryLocation implements IUserOnlineLocation{
    
    public function cache(UserOnline $user) {}
    
    public function get(UserOnline $user, $languageVariable = '') {
        if ($category = CategoryHandler::getInstance()->getCategory($user->objectID)) {
            $category = new NewsCategory($category);
            if ($category->getPermission('canView')) {
                return WCF::getLanguage()->getDynamicVariable($languageVariable, array('category' => $category));
            }
        }
        return '';
	}
}