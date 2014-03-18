<?php
namespace cms\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;

use cms\data\restore\RestoreAction;
use cms\data\restore\RestoreList;
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
            if(CMS_AUTOMATIC_BACKUP_DELETE != 0){
                $deleteTime = (TIME_NOW - (CMS_AUTOMATIC_BACKUP_DELETE * 86400));
                $list = new RestoreList();
                $list->getConditionBuilder()->add('time <= ?', array($deleteTime));
                $list->readObjects();
                $action = new RestoreAction($list->getObjects(), 'delete');
                $action->executeAction();
            }
            
        }
}   