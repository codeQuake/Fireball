<?php

namespace cms\system\content\type\graph;

use cms\data\content\Content;
use cms\system\content\type\AbstractContentType;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

class PieChartContentType extends AbstractContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-pie-chart';
	
	/**
	 * @inheritDoc
	 */
	public function validate(&$data) {
		parent::validate($data);
		
		if (empty($data['graphOptions']['title'])) {
			throw new UserInputException('contentData[graphOptions][title][0]');
		}
		
		if (empty($data['graphOptions']['value'])) {
			throw new UserInputException('contentData[graphOptions][value][0]');
		}
		
		if (count($data['graphOptions']['title']) !== count($data['graphOptions']['value'])) {
			throw new UserInputException('value', 'CountNotEqual');
		}
		
		$total = 0;
		foreach ($data['graphOptions']['value'] as $key => $value) {
			if (!is_numeric($value)) {
				throw new UserInputException('contentData[graphOptions][value][' . $key . ']', 'notNumberic');
			}
			
			$total += $value;
		}
		
		$data['total'] = $total;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		$options = [];
		foreach ($content->contentData['graphOptions']['title'] as $key => $title) {
			$options[$title] = $content->contentData['graphOptions']['value'][$key];
		}
		
		WCF::getTPL()->assign([
			'graphOptions' => $options
		]);
		
		return parent::getOutput($content);
	}
}
