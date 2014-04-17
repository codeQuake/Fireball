<?php
namespace cms\acp\action;
use cms\data\restore\RestoreAction;
use cms\system\export\CMSExportHandler;
use wcf\action\AbstractAction;

class CMSExportAction extends AbstractAction{

    public function execute(){
        parent::execute();
        $filename = CMSExportHandler::getInstance()->getExportArchive();
        $data = array('filename' => $filename,
                      'time' => TIME_NOW);
        $action = new RestoreAction(array(), 'create', array('data' => $data));
        $action->executeAction();
        
        $this->executed();
        // headers for downloading file
		header('Content-Type: application/x-gzip; charset=utf8');
		header('Content-Disposition: attachment; filename="CMS-Export.tar.gz"');
		readfile($filename);
    }
}
