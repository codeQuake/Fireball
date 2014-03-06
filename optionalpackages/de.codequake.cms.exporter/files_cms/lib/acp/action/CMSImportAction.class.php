<?php
namespace cms\acp\action;
use wcf\action\AbstractAction;
use cms\system\export\CMSExportHandler;
use cms\system\export\CMSImportHandler;
use cms\data\restore\RestoreAction;

class CMSImportAction extends AbstractAction{

    public function execute(){
        parent::execute();
        $filename = 'C:/xampp/htdocs/export/Arschi.tar.gz';
        CMSImportHandler::getInstance()->handleImport($filename);
    }
    
}