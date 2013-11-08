<?php
namespace cms\form;
use cms\data\category\NewsCategoryNodeTree;
use cms\data\category\NewsCategory;
use cms\data\news\NewsAction;
use wcf\form\MessageForm;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\system\breadcrumb\Breadcrumb;
use wcf\system\request\LinkHandler;
use wcf\system\exception\UserInputException;
use wcf\system\category\CategoryHandler;
use wcf\util\ArrayUtil;

class NewsAddForm extends MessageForm{

    public $action = 'add';
    
    public $categoryIDs = array();
    public $categoryList = array();
    public $activeMenuItem = 'cms.page.news';
    public $enableTracking = true;
    public $neededPermissions = array('user.cms.news.canAddNews');
    public $enableMultilingualism = true;
    
    
    public $tags = array();
    
    public function readFormParameters(){
        parent::readFormParameters();
        if (isset($_POST['tags']) && is_array($_POST['tags'])) $this->tags = ArrayUtil::trim($_POST['tags']);
    }
    
    
    public function readParameters(){
        parent::readParameters();
        if (isset($_REQUEST['categoryIDs']) && is_array($_REQUEST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_REQUEST['categoryIDs']);
    }
    public function readData(){
        parent::readData();
         WCF::getBreadcrumbs()->add(new Breadcrumb(WCF::getLanguage()->get('cms.page.news'), 
                                                            LinkHandler::getInstance()->getLink('NewsCategoryList', array('application' => 'cms'))));
        
        $excludedCategoryIDs = array_diff(NewsCategory::getAccessibleCategoryIDs(), NewsCategory::getAccessibleCategoryIDs(array('canAddNews')));
        $categoryTree = new NewsCategoryNodeTree('de.codequake.cms.category.news', 0, false, $excludedCategoryIDs);
        $this->categoryList = $categoryTree->getIterator();
        $this->categoryList->setMaxDepth(0);
        
        if (empty($_POST)) {
			// multilingualism
			if (!empty($this->availableContentLanguages)) {
				if (!$this->languageID) {
					$language = LanguageFactory::getInstance()->getUserLanguage();
					$this->languageID = $language->languageID;
				}
				
				if (!isset($this->availableContentLanguages[$this->languageID])) {
					$languageIDs = array_keys($this->availableContentLanguages);
					$this->languageID = array_shift($languageIDs);
				}
			}
		}
    }
    
    public function validate(){
        parent::validate();
        //categories
        if (empty($this->categoryIDs)) {
			throw new UserInputException('categoryIDs');
		}
        
        foreach ($this->categoryIDs as $categoryID) {
			$category = CategoryHandler::getInstance()->getCategory($categoryID);
			if ($category === null) throw new UserInputException('categoryIDs');
			
			$category = new NewsCategory($category);
			if (!$category->isAccessible() || !$category->getPermission('canAddNews')) throw new UserInputException('categoryIDs');
		}
    }
    
    public function save(){
        parent::save();
        $data = array('languageID' => $this->languageID,
                       'subject' => $this->subject,
                       'time' => TIME_NOW,
                       'message' => $this->text,
                       'userID' => WCF::getUser()->userID,
                       'username' => WCF::getUser()->username,
                       'enableBBCodes' => $this->enableBBCodes,
			           'enableHtml' => $this->enableHtml,
			           'enableSmilies' => $this->enableSmilies,
                       'lastChangeTime' => TIME_NOW);
        $newsData = array('data' => $data,
                          'tags' => array(),
                          'categoryIDs' => $this->categoryIDs);
        $newsData['tags'] = $this->tags;
        
        $action = new NewsAction(array(), 'create', $newsData);
        $resultValues = $action->executeAction();
        $this->saved();
                       
        HeaderUtil::redirect(LinkHandler::getInstance()->getLink('News', array(
                                                                'application' => 'cms',
                                                                'object' => $resultValues['returnValues']
                                                                )));
        exit;
    }
    
    public function assignVariables(){
        parent::assignVariables();
        WCF::getTPL()->assign(array('categoryList' => $this->categoryList,
                                    'categoryIDs' => $this->categoryIDs,
                                    'action' => $this->action,
                                    'tags'      => $this->tags,));
    }
}