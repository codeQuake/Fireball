<?php
namespace cms\page;
use wcf\page\AbstractPage;
use cms\data\file\File;
use cms\data\file\FileEditor;
use wcf\util\FileReader;
use cms\system\counter\VisitCountHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class FileDownloadPage extends AbstractPage{
    
    public $file = null;
    public $fileReader = null;
    public $useTemplate = false;
    public static $inlineMimeTypes = array('image/gif', 'image/jpeg', 'image/png', 'application/pdf', 'image/pjpeg');
    
    public function readParameters(){
        parent::readParameters();
        $fileID = 0;
        if(isset($_REQUEST['id'])) $fileID = intval($_REQUEST['id']);
        $this->file = new File($fileID);        
        if($this->file === null) throw new IllegalLinkException();
        
        if(!$this->file->getPermission('canDownloadFile')) throw new PermissionDeniedException();
    }
    
    public function readData(){
        parent::readData();
        VisitCountHandler::getInstance()->count();
        $this->fileReader = new FileReader(CMS_DIR.'files/'.$this->file->filename, array('filename' => $this->file->title,
                                                                        'mimeType' => $this->file->type,
                                                                        'filesize' => $this->file->size,
                                                                        'showInline' => (in_array($this->file->type, self::$inlineMimeTypes)),
                                                                        'enableRangeSupport' => false,
                                                                        'lastModificationTime' => TIME_NOW,
                                                                        'expirationDate' => TIME_NOW + 31536000,
                                                                        'maxAge' => 31536000));
        
        $editor = new FileEditor($this->file);
        $downloads = $this->file->downloads + 1;
        $editor->update(array('downloads' => $downloads));
    }
    
    public function show(){
        parent::show();
        $this->fileReader->send();
        exit;
    }
}