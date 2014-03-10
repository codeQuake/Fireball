<?php
namespace cms\acp\form;
use wcf\form\AbstractForm;
use wcf\util\StringUtil;
use wcf\system\WCF;
use wcf\system\exception\UserInputException;
use cms\data\feed\FeedAction;
use wcf\system\language\LanguageFactory;
use wcf\system\category\CategoryHandler;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms.news.rss
 */

class FeedAddForm extends AbstractForm{

    public $templateName = 'feedAdd';
    public $neededPermissions = array('admin.cms.news.canAddFeed');
    public $activeMenuItem = 'cms.acp.menu.link.cms.feed.add';
    
    public $title = '';
    public $feedUrl = '';
    public $categoryID = '';
    public $languageID = '';
    public $availableContentLanguages = array();
    public $categoryList = null;
    
   public function readData(){
        parent::readData();
        $this->availableContentLanguages = LanguageFactory::getInstance()->getContentLanguages();
        $this->categoryList = CategoryHandler::getInstance()->getCategories('de.codequake.cms.category.news');
    }
    
    public function readFormParameters(){
        parent::readFormParameters();
        
        if(isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
        if(isset($_POST['feedUrl'])) $this->feedUrl = StringUtil::trim($_POST['feedUrl']);
        if(isset($_POST['categoryID'])) $this->categoryID = intval($_POST['categoryID']);
        if(isset($_POST['languageID'])) $this->languageID = intval($_POST['languageID']);
    }
    
    public function save(){
        parent::save();
        
        $objectAction = new FeedAction(array(), 'create', array('data' => array('title' => $this->title, 'feedUrl' => $this->feedUrl, 'lastCheck' => TIME_NOW, 'categoryID' => $this->categoryID, 'languageID' => $this->languageID)));
        $objectAction->executeAction();
       
        $this->saved();
        WCF::getTPL()->assign('success', true);
        
        $this->title = '';
        $this->feedUrl = '';
    }
    
    public function validate(){
        parent::validate();
        if(!simplexml_load_file($this->feedUrl)) throw new UserInputException('feedUrl', 'noFeed');
    }
    
    public function assignVariables(){
        parent::assignVariables();
        
        WCF::getTPL()->assign(array('categories' => $this->categoryList, 'title' => $this->title, 'feedUrl' => $this->feedUrl, 'action' => 'add', 'availableContentLanguages' => $this->availableContentLanguages, 'categoryID' => $this->categoryID, 'languageID' => $this->languageID));
    }
}