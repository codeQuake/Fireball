<?php
namespace cms\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;

use cms\data\restore\RestoreAction;
use cms\system\export\CMSExportHandler;

class BackupCronjob extends AbstractCronjob{
    
        public function execute(Cronjob $cronjob){
            parent::execute($cronjob);
            if(CMS_AUTOMATIC_EXPORT){
                $filename = CMSExportHandler::getInstance()->getExportArchive();
                $data = array('filename' => $filename,
                              'time' => TIME_NOW);
                $action = new RestoreAction(array(), 'create', array('data' => $data));
                $action->executeAction();
            }
            
        }
}   