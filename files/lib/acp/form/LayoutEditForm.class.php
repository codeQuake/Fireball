<?php
namespace cms\acp\form;
use cms\data\layout\Layout;
use cms\data\layout\LayoutAction;
use wcf\form\AbstractForm;
use wcf\util\StringUtil;
use wcf\system\WCF;

class LayoutEditForm extends LayoutAddForm{
    
    public $layoutID = 0;
    public $layout = null;
    
    public function readData(){
        parent::readData();
        if(isset($_REQUEST['id'])) $this->layoutID = intval($_REQUEST['id']);
        $this->layout = new Layout($this->layoutID);
        $this->title = $this->layout->title;
        $this->data = @unserialize($this->layout->data);
    }
    
    public function readFormParameters(){
        parent::readFormParameters();
        if(isset($_REQUEST['id'])) $this->layoutID = intval($_REQUEST['id']);
        if(isset($_POST['data'])) $this->data = $_POST['data'];
        if(isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
    }
    
    public function assignVariables(){
        parent::assignVariables();
        WCF::getTPL()->assign(array('action' => 'edit', 'layoutID' => $this->layoutID));
    }
    
    public function save(){
        AbstractForm::save();
        $objectAction = new LayoutAction(array($this->layoutID), 'update', array('data' => array('title' => $this->title, 'data' => serialize($this->data))));
        $objectAction->executeAction();
        
        $this->saved();
        WCF::getTPL()->assign('success', true);
    }
}