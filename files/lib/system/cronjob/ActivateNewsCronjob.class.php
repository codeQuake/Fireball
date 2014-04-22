<?php
namespace cms\system\cronjob;

use cms\data\news\NewsAction;
use cms\data\news\NewsList;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;

class ActivateNewsCronjob extends AbstractCronjob {

	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		$list = new NewsList();
		$list->getConditionBuilder()->add('isDisabled = ?', array(
			1
		));
		$list->getConditionBuilder()->add('time <= ?', array(
			TIME_NOW
		));
		$list->readObjects();
		$list = $list->getObjects();
		
		$action = new NewsAction($list, 'publish');
		$action->executeAction();
	}
}
