<?php
namespace cms\acp\action;
use wcf\action\AbstractAction;
use cms\system\export\CMSExportHandler;

class CMSExportAction extends AbstractAction{

    public function execute(){
        parent::execute();
        $filename = CMSExportHandler::getInstance()->getExportArchive();
        $this->executed();
        // headers for downloading file
		header('Content-Type: application/x-gzip; charset=utf8');
		header('Content-Disposition: attachment; filename="CMS-Export.tar.gz"');
		readfile($filename);
		// delete temp file
		@unlink($filename);
    }
    
}