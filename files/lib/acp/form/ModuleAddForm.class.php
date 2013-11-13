<?php
namespace cms\acp\form;
use cms\data\module\ModuleAction;
use wcf\util\StringUtil;
use wcf\form\AbstractForm;
use wcf\system\WCF;

class ModuleAddForm extends AbstractForm{
    
    public $neededPermissions = array('cms.acp.content.canManageModule');
    public $activeMenuItem = 'cms.acp.menu.link.cms.module.add';
    public $action = 'add';
    
    public $title = '';
    public $phpCode = '';
    public $tplCode = '';
    
    public function readFormParameters(){
        parent::readFormParameters();
        if(isset($_POST['php'])) $this->phpCode = StringUtil::trim($_POST['php']);
        if(isset($_POST['tpl'])) $this->tplCode = StringUtil::trim($_POST['tpl']);
        if(isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
    }
    
    public function save(){
        parent::save();
        
        $data = array('moduleTitle' => $this->title,
                      'php' => $this->phpCode,
                      'tpl' => $this->tplCode);
        $action  = new ModuleAction(array(), 'create', $data);
        $action->executeAction();
        
        $this->saved();
        
        WCF::getTPL()->assign('success', true);
        
        $this->title = $this->phpCode = $this->tplCode = '';
    }
    
    public function assignVariables(){
        parent::assignVariables();
        WCF::getTPL()->assign(array('title' => $this->title,
                                    'php' => $this->phpCode,
                                    'tpl' => $this->tplCode));
    }
}