<?php
namespace cms\data\content;
use wcf\data\AbstractDatabaseObjectAction;


class ContentAction extends AbstractDatabaseObjectAction{
    protected $className = 'cms\data\content\ContentEditor';
    
    protected $permissionsCreate = array('admin.content.cms.canAddContent');
    protected $permissionsDelete = array('admin.content.cms.canDeleteContent');
    protected $permissionsUpdate = array('admin.content.cms.canEditContent');
    
    public function create(){
        if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
            $this->parameters['data']['attachments'] = count($this->parameters['attachmentHandler']);
        }
        return $content = parent::create();
    }
    
    public function update(){
        if (isset($this->parameters['data'])) {
            if (isset($this->parameters['attachmentHandler']) && $this->parameters['attachmentHandler'] !== null) {
                $this->parameters['data']['attachments'] = count($this->parameters['attachmentHandler']);
            }
            parent::update();
        }
        else {
            if (empty($this->objects)) {
                $this->readObjects();
            }
        }
    }
    
    public function delete(){
        parent::delete();
    }
}