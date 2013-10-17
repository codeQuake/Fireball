<?php
namespace cms\page;
use wcf\page\AbstractPage;
use cms\data\file\File;
use cms\data\file\FileEditor;
use wcf\util\FileReader;
use wcf\system\exception\IllegalLinkException;

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
        //todo permission check
    }
    
    public function readData(){
        parent::readData();
        $this->fileReader = new FileReader(CMS_DIR.'files/'.$this->file->filename, array('filename' => $this->file->title,
                                                                        'mimeType' => $this->file->type,
                                                                        'filesize' => $this->file->size,
                                                                        'showInline' => (in_array($this->file->type, self::$inlineMimeTypes)),
                                                                        'enableRangeSupport' => false,
                                                                        'lastModificationTime' => TIME_NOW,
                                                                        'expirationDate' => TIME_NOW + 31536000,
                                                                        'maxAge' => 31536000));
        
    }
    
    public function show(){
        parent::show();
        $editor = new FileEditor($this->file);
        $editor->update(array('downloads' => $this->file->downloads++));
        $this->fileReader->send();
        exit;
    }
}