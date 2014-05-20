<?php
namespace cms\data\modification\log;

use wcf\data\DatabaseObjectDecorator;
use wcf\system\WCF;

class ViewablePageModificationLog extends DatabaseObjectDecorator {
	protected static $baseClass = 'wcf\data\modification\log\ModificationLog';

	public function __toString() {
		return WCF::getLanguage()->getDynamicVariable('cms.page.log.' . $this->action, array(
			'additionalData' => $this->additionalData
		));
	}
}
