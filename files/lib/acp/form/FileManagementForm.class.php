<?php
namespace cms\acp\form;
use wcf\form\AbstractForm;
use wcf\system\WCF;
use cms\data\file\FileAction;
use wcf\util\ArrayUtil;
use cms\data\file\FileList;
use cms\data\folder\FolderList;
use wcf\system\exception\UserInputException;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class FileManagementForm extends AbstractForm{
    
    public $templateName = 'fileAdd';
    public $neededPermissions = array('admin.cms.file.canAddFile');
    public $activeMenuItem = 'cms.acp.menu.link.cms.file.management';
    public $folderID = 0;
    public $file = null;
    public $fileList = null;
    public $folderList = null;
    
    public function readFormParameters(){
        parent::readFormParameters();
        if (isset($_FILES['file'])) $this->file = $_FILES['file'];
        if (isset($_POST['folderID'])) $this->folderID = intval($_POST['folderID']);
    }
    
    public function validate(){
        parent::validate();
        //check if file is given
        if (empty($this->file)) {
            throw new UserInputException('file', 'empty');
        }
        if (empty($this->file['tmp_name'])) throw new UserInputException('file', 'empty');
        $allowedTypes = ArrayUtil::trim(explode("\n", WCF::getSession()->getPermission('admin.cms.file.allowedTypes')));
        $tmp = explode('.', $this->file['name']);
        $fileType = array_pop($tmp);
        if (!in_array($fileType, $allowedTypes))  throw new UserInputException('file', 'invalid');
    }
    
    public function save(){
        parent::save();
        $tmp = explode('.',$this->file['name']);
        $filename = 'FB-File-'.md5($this->file['tmp_name'].time()).'.'.array_pop($tmp);
        $path = CMS_DIR.'files/'.$filename;
        move_uploaded_file($this->file['tmp_name'], $path);
        
        $data = array('title' => $this->file['name'],
                      'folderID' => $this->folderID,
                      'filename' => $filename,
                      'type' => $this->file['type'],
                      'size' => $this->file['size']);
        $action = new FileAction(array(), 'create', array('data' => $data));
        $action->executeAction();
        
        $this->saved();
        WCF::getTPL()->assign('success', true);
        
        $this->file = null;
    }
    
    public function readData(){
        parent::readData();
        $list = new FileList();
        $list->readObjects();
        $this->fileList = $list->getObjects();
        
        $folders = new FolderList();
        $folders->readObjects();
        $this->folders = $folders->getObjects();
        
    }
    
    public function assignVariables(){
        parent::assignVariables();
        WCF::getTPL()->assign(array('fileList' => $this->fileList,
                                    'folderID' => $this->folderID,
                                    'folderList' => $this->folders));
    }   
}