<?php

namespace cms\system\template;

use wcf\system\template\TemplateEngine;

class EnvironmentTemplateEngine extends TemplateEngine {
	/**
	 * Set the environment
	 *
	 * @param $environment
	 */
	public function setEnvironment($environment) {
		$this->environment = $environment;
	}
	
	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->registerPrefilter(['event', 'hascontent', 'lang']);
		$this->assign([
			'__wcf' => null,
			'__wcfVersion' => LAST_UPDATE_TIME
		]);
		
		parent::init();
	}
}
