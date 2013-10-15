<?php
namespace cms\acp\form;
use cms\data\stylesheet\StylesheetList;
use cms\data\layout\LayoutAction;
use wcf\form\AbstractForm;
use wcf\util\StringUtil;
use wcf\system\WCF;

class LayoutAddForm extends AbstractForm{

    public $templateName = 'layoutAdd';
    public $neededPermissions = array('admin.cms.style.canAddLayout');
    public $activeMenuItem = 'cms.acp.menu.link.cms.layout.add';
    
    public $title = '';
    public $data = array();
    
    public $stylesheetList = array();
    
    public function readData(){
        parent::readData();
        $this->stylesheetList = new StylesheetList();
        $this->stylesheetList->readObjects();
        $this->stylesheetList = $this->stylesheetList->getObjects();
    }
    
    public function readFormParameters(){
        parent::readFormParameters();
        
        if(isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
        if(isset($_POST['data'])) $this->data = $_POST['data'];
    }
    
    public function save(){
        parent::save();
        
        $objectAction = new LayoutAction(array(), 'create', array('data' => array('title' => $this->title, 'data' => serialize($this->data))));
        $objectAction->executeAction();
       
        $this->saved();
        WCF::getTPL()->assign('success', true);
        
        $this->title = '';
        $this->data = array();
    }
    
    public function assignVariables(){
        parent::assignVariables();
        
        WCF::getTPL()->assign(array('title' => $this->title, 'data' => $this->data, 'action' => 'add', 'sheetList' => $this->stylesheetList));
    }
}