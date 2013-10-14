<?php
namespace cms\acp\page;
use cms\data\content\section\ContentContentSectionList;
use cms\data\content\Content;
use wcf\page\SortablePage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

class ContentSectionListPage extends SortablePage{
    
    public $objectListClassName = 'cms\data\content\section\ContentContentSectionList';
    public $activeMenuItem = 'cms.acp.menu.link.cms.content.list';
    public $neededPermissions = array('admin.cms.content.canListContentSection');
    public $templateName = 'contentSectionList';
    public $defaultSortfield = 'showOrder';
    public $validSortFields = array('sectionID', 'showOrder');
    public $objectList = array();
    public $contentID = 0;
    public $content = null;
    
    public function readParameters(){
        parent::readParameters();
        $content = null;
        
        if(isset($_GET['id'])) $this->contentID = intval($_GET['id']);
        if($this->contentID == 0) throw new IllegalLinkException();
        $this->content = new Content($this->contentID);
        if($this->content === null) throw new IllegalLinkException();
    }
    
    public function initObjectList(){
        $this->objectList = new ContentContentSectionList($this->contentID);
    }
    
    public function assignVariables(){
        parent::assignVariables();
        WCF::getTPL()->assign(array('contentID' => $this->contentID,
                                    'content' => $this->content));
    }
}