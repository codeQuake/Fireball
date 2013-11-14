<?php
namespace cms\acp\form;
use cms\data\module\Module;
use cms\data\module\ModuleAction;
use wcf\util\StringUtil;
use wcf\form\AbstractForm;
use wcf\system\WCF;

class ModuleEditForm extends ModuleAddForm{
    
    public $neededPermissions = array('admin.cms.content.canManageModule');
    public $activeMenuItem = 'cms.acp.menu.link.cms.module.add';
    public $action = 'add';
    
    public $title = '';
    public $phpCode = '';
    public $tplCode = '';
    public $moduleID = 0;
    public $module = null;
    
    public function readData(){
        parent::readData();
        if(isset($_REQUEST['id'])) $this->moduleID = intval($_REQUEST['id']);
        $this->module = new Module($this->moduleID);
        $this->title = $this->module->moduleTitle;
        $this->phpCode = $this->module->php;
        $this->tplCode = $this->module->tpl;
    }
    
    public function readFormParameters(){
        parent::readFormParameters();
        if(isset($_REQUEST['id'])) $this->moduleID = intval($_REQUEST['id']);
        $this->module = new Module($this->moduleID);
    }
    
    public function save(){
        AbstractForm::save();
        
        $data = array('data' => array('moduleTitle' => $this->title),
                      'source' => array(
                                      'php' => $this->phpCode,
                                      'tpl' => $this->tplCode));
        $action  = new ModuleAction(array($this->sheet), 'update', $data);
        $action->executeAction();
        
        $this->saved();
        
        WCF::getTPL()->assign('success', true);
        
        $this->title = $this->phpCode = $this->tplCode = '';
    }
    
    public function assignVariables(){
        parent::assignVariables();
        WCF::getTPL()->assign(array('title' => $this->title,
                                    'phpCode' => $this->phpCode,
                                    'tplCode' => $this->tplCode,
                                    'moduleID' => $this->module->moduleID));
    }
}