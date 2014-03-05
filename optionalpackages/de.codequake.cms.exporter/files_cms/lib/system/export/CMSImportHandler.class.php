<?php
namespace cms\system\export;
use wcf\system\SingletonFactory;
use wcf\system\exception\UserInputException;
use wcf\system\exception\SystemException;
use wcf\util\XML;
use wcf\system\io\Tar;
use wcf\data\object\type\ObjectTypeCache;

use cms\data\page\PageList;
use cms\data\page\PageAction;
use cms\data\content\ContentList;
use cms\data\content\ContentAction;
use cms\data\content\section\ContentSectionList;
use cms\data\content\section\ContentSectionAction;
use cms\data\folder\FolderList;
use cms\data\folder\FolderAction;
use cms\data\file\FileList;
use cms\data\file\FileAction;
use cms\data\layout\LayoutList;
use cms\data\layout\LayoutAction;
use cms\data\stylesheet\StylesheetList;
use cms\data\stylesheet\StylesheetAction;

class CMSImportHandler extends SingletonFactory{
    public $data = array();
    
    public function init(){
        
    }
    
       
    public function handleImport($filename){
        if($this->filename = '') throw new UserInputException('filename');
        $this->openTar($filename);
        $this->importPages();
        $this->importContents();
        $this->importFolders();
        $this->importFiles();
        $this->importStylesheets();
        $this->importLayouts();
        $this->importModules();
    }
    
    protected function importPages(){
        //delete all pages
        $list = new PageList();
        $list->readObjects();
        $action = new PageAction($list->getObjects(), 'delete');
        $action->executeAction();
        
        //import
        if(isset($this->data['pages'])){
            foreach($this->data['pages'] as $page){
                $data = $page;
                $action = new PageAction(array(), 'create', array('data' => $data));
                $action->executeAction();
            }
        }
    }
    
    protected function importFolders(){
        //delete all folders
        $list = new FolderList();
        $list->readObjects();
        $action = new FolderAction($list->getObjects(), 'delete');
        $action->executeAction();
        
        //import
        if(isset($this->data['folders'])){
            foreach($this->data['folders'] as $folder){
                $data = $folder;
                $action = new FolderAction(array(), 'create', array('data' => $data));
                $action->executeAction();
            }
        }
    }
    
    protected function importFiles(){
        //delete all files
        $list = new FileList();
        $list->readObjects();
        $action = new FileAction($list->getObjects(), 'delete');
        $action->executeAction();
        
        //import
        if(isset($this->data['files'])){
            foreach($this->data['files'] as $file){
                $data = $file;
                $action = new FileAction(array(), 'create', array('data' => $data));
                $action->executeAction();
            }
        }
    }
    
    protected function importLayouts(){
        //delete all layouts
        $list = new LayoutList();
        $list->readObjects();
        $action = new LayoutAction($list->getObjects(), 'delete');
        $action->executeAction();
        
        //import
        if(isset($this->data['layouts'])){
            foreach($this->data['layouts'] as $layout){
                $data = $layout;
                $action = new LayoutAction(array(), 'create', array('data' => $data));
                $action->executeAction();
            }
        }
    }
    
    protected function importStylesheets(){
        //delete all sheets
        $list = new StylesheetList();
        $list->readObjects();
        $action = new StylesheetAction($list->getObjects(), 'delete');
        $action->executeAction();
        
        //import
        if(isset($this->data['stylesheets'])){
            foreach($this->data['stylesheets'] as $sheet){
                $data = $sheet;
                $action = new StylesheetAction(array(), 'create', array('data' => $data));
                $action->executeAction();
            }
        }
    }
    
    
    protected function importModules(){
        //delete all modules
        $list = new ModuleList();
        $list->readObjects();
        $action = new ModuleAction($list->getObjects(), 'delete');
        $action->executeAction();
        
        //import
        if(isset($this->data['modules'])){
            foreach($this->data['modules'] as $mod){
                $data = $mod;
                $action = new ModuleAction(array(), 'create', array('data' => $data));
                $action->executeAction();
            }
        }
    }
    
    protected function importContents(){
        //delete all contents
        $list = new ContentList();
        $list->readObjects();
        $action = new ContentAction($list->getObjects(), 'delete');
        $action->executeAction();
        
        //delete all sections
        $list = new ContentSectionList();
        $list->readObjects();
        $action = new ContentSectionAction($list->getObjects(), 'delete');
        $action->executeAction();
        
        //import
        if(isset($this->data['contents'])){
            foreach($this->data['contents'] as $content){
                $data = array();
                $sectionData = array();
                foreach($content as $key => $value){
                    if($key == 'sections'){
                        //handle sections
                        $sectionData[$key] =  $value;
                    }
                    else $data[$key] = $value;
                }
                $action = new ContentAction(array(), 'create', array('data' => $data));
                $action->executeAction();
                if(isset($sectionData['sections'])){
                    foreach($sectionData['sections'] as $section){
                        $data = $section;
                        $data['sectionTypeID'] = ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.codequake.cms.section.type', $section['sectionType']);
                        unset($data['sectionType']);
                        $action = new ContentSectionAction(array(), 'create', array('data' => $data));
                        $action->executeAction();
                    }
                }
                
            }
        }
    }
    
    protected function openTar($filename){
        $tar = new Tar($filename);
        $this->data = $this->readData($tar);
        $tar->close();
    }
    
    protected function readData($tar){
        $xml = 'cmsData.xml';
        if($tar->getIndexByFileName($xml) === false){
            throw new SystemException("Unable to find required file '".$xml."' in the import archive");
        }
        $xmlData = new XML();
        $xmlData->loadXML($xml, $tar->extractToString($tar->getIndexByFileName($xml)));
        $xpath = $xmlData->xpath();
        $root = $xpath->query('/ns:data')->item(0);
        $items = $xpath->query('child::*', $root);
        $data = array();
        $i = 0;
        $j = 0;
        foreach($items as $item){
            switch($item->tagName){
                case 'page':
                    foreach($xpath->query('child::*', $item) as $child){
                        $data['pages'][$i][$child->tagName] = $child->nodeValue;
                    }
                break;
                case 'content':
                    foreach($xpath->query('child::*', $item) as $child){
                        if($child->tagName != 'sections'){
                            $data['contents'][$i][$child->tagName] = $child->nodeValue;
                        }
                        else{
                            foreach($xpath->query('child::*', $child) as $section){
                                switch($section->tagName){
                                    case 'section':
                                        foreach($xpath->query('child::*', $section) as $sectionItem){
                                            $data['contents'][$i]['sections'][$j][$sectionItem->tagName] = $sectionItem->nodeValue;
                                        }
                                    break;
                                    default: break;
                                }
                                $j++;
                            }
                        }
                    }
                break;
                case 'folder':
                    foreach($xpath->query('child::*', $item) as $child){
                        $data['folders'][$i][$child->tagName] = $child->nodeValue;
                    }
                break;
                
                case 'file':
                    foreach($xpath->query('child::*', $item) as $child){
                        $data['files'][$i][$child->tagName] = $child->nodeValue;
                    }
                break;
                case 'stylesheet':
                    foreach($xpath->query('child::*', $item) as $child){
                        $data['stylesheets'][$i][$child->tagName] = $child->nodeValue;
                    }
                break;
                case 'layout':
                    foreach($xpath->query('child::*', $item) as $child){
                        $data['layouts'][$i][$child->tagName] = $child->nodeValue;
                    }
                break;
                case 'module':
                    foreach($xpath->query('child::*', $item) as $child){
                        $data['modules'][$i][$child->tagName] = $child->nodeValue;
                    }
                break;
            }
            $i++;
        }
        return $data;
    }
}