<?php
namespace cms\data\module;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\template\TemplateAction;
use wcf\util\FileUtil;

class ModuleAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\module\ModuleEditor';
    protected $permissionsDelete = array('admin.cms.content.canManageModule');
    protected $requireACP = array('delete');

    public function create(){
        $data = $this->parameters['data'];
        $source = $this->parameters['source'];
        if(!empty($source['tpl'])){
            $templateName = 'cms_'.$data['moduleTitle'].'_'.md5($data['moduleTitle'].TIME_NOW);
            $tplData = array('data' => array('application' => 'cms',
                                            'templateName' => $templateName,
                                            'packageID' => PACKAGE_ID,
                                            'templateGroupID' => null),
                            'source' => $source['tpl']);
            $tplAction = new TemplateAction(array(), 'create', $tplData);
            $tplAction->executeAction();
            
            $this->parameters['data']['tpl'] = $templateName;
        }
        
        if(!empty($source['php'])){
            $phpFileName = 'cms_'.$data['moduleTitle'].'_'.md5($data['moduleTitle'].TIME_NOW);
            
            file_put_contents(CMS_DIR.'files/php/'.$phpFileName.'.php', $source['php']);
            FileUtil::makeWritable(CMS_DIR.'files/php/'.$phpFileName.'.php');
            
            $this->parameters['data']['php'] = $phpFileName.'.php';
        }
        
        $module = parent::create();
        return $module;
    }
}