<?php
namespace cms\data\module;
use wcf\data\template\Template;
use wcf\data\template\TemplateAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;
use wcf\util\FileUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

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
    
    public function update(){
        
        
        foreach ($this->objects as $module){
            if(isset($this->parameters['source']['tpl'])){
                //create new
                if($module->tpl === null){
                        $templateName = 'cms_'.$this->parameters['data']['moduleTitle'].'_'.md5($this->parameters['data']['moduleTitle'].TIME_NOW);
                        $tplData = array('data' => array('application' => 'cms',
                                                    'templateName' => $templateName,
                                                    'packageID' => PACKAGE_ID,
                                                    'templateGroupID' => null),
                                        'source' => $this->parameters['source']['tpl']);
                        $tplAction = new TemplateAction(array(), 'create', $tplData);
                        $tplAction->executeAction();
            
                        $this->parameters['data']['tpl'] = $templateName;
                }
                //edit
                else{
                    $data = array('templateName' => $module->tpl,
                                  'packageID' => PACKAGE_ID,
                                  'templateGroupID' => null);
                                  
                    $sql = "SELECT templateID FROM wcf".WCF_N."_template WHERE templateName = ?";
                    $statement = WCF::getDB()->prepareStatement($sql);
                    $statement->execute(array($module->tpl));
                    $row = $statement->fetchArray();        
                    $tpl = new Template($row['templateID']);
                    
                    $tplAction = new TemplateAction(array($tpl), 'update', array('data' => $data, 'source' => $this->parameters['source']['tpl']));
                    $tplAction->executeAction();
                
                }
            }
            
            if(isset($this->parameters['source']['php'])){
                //create new
                if($module->php === null){
                    $phpFileName = 'cms_'.$this->parameters['data']['moduleTitle'].'_'.md5($this->parameters['data']['moduleTitle'].TIME_NOW);
            
                    file_put_contents(CMS_DIR.'files/php/'.$phpFileName.'.php', $this->parameters['source']['php']);
                    FileUtil::makeWritable(CMS_DIR.'files/php/'.$phpFileName.'.php');
            
                    $this->parameters['data']['php'] = $phpFileName.'.php';
                }
                //edit
                else{
                    file_put_contents(CMS_DIR.'files/php/'.$module->php, $this->parameters['source']['php']);
                    FileUtil::makeWritable(CMS_DIR.'files/php/'.$module->php);
                }
            }
        }
        
        parent::update();
    }
    
    
    public function delete(){
        foreach ($this->objects as $module){
            if($module->tpl !== null){
                //delete TPL
                $sql = "SELECT templateID FROM wcf".WCF_N."_template WHERE templateName = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute(array($module->tpl));
                $row = $statement->fetchArray();        
                $tpl = new Template($row['templateID']);
                $tplAction = new TemplateAction(array($tpl), 'delete', array());
                $tplAction->executeAction();
            }
            
            if($module->php !== null){
                //delete PHP
                if(file_exists(CMS_DIR.'files/php/'.$module->php))unlink(CMS_DIR.'files/php/'.$module->php);
            }
        }
        parent::delete();
    }
}