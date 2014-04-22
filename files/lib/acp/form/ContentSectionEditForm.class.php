<?php
namespace cms\acp\form;

use cms\data\content\section\ContentSection;
use cms\data\content\section\ContentSectionAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractForm;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class ContentSectionEditForm extends ContentSectionAddForm {
	public $sectionID = 0;
	public $section = null;

	public function readData() {
		AbstractForm::readData();
		$this->objectTypeList = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.section.type');
		if (isset($_REQUEST['id'])) $this->sectionID = intval($_REQUEST['id']);
		$this->section = new ContentSection($this->sectionID);
		$this->contentID = $this->section->contentID;
		$this->showOrder = $this->section->showOrder;
		$this->cssID = $this->section->cssID;
		$this->cssClasses = $this->section->cssClasses;
	}

	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['id'])) $this->sectionID = intval($_REQUEST['id']);
		$this->section = new ContentSection($this->sectionID);
		$this->objectType = ObjectTypeCache::getInstance()->getObjectType($this->section->sectionTypeID);
		$this->objectTypeProcessor = $this->objectType->getProcessor();
		$this->objectTypeProcessor->setAction('edit');
		$this->objectTypeProcessor->readParameters();
		$this->objectTypeProcessor->readData($this->sectionID);
		$this->submit();
	}

	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_REQUEST['id'])) $this->sectionID = intval($_REQUEST['id']);
		$this->section = new ContentSection($this->sectionID);
	}

	public function save() {
		AbstractForm::save();
		$this->objectTypeProcessor->saveFormData();
		$data = array(
			'contentID' => $this->contentID,
			'showOrder' => $this->showOrder,
			'cssID' => $this->cssID,
			'cssClasses' => $this->cssClasses,
			'sectionTypeID' => $this->objectType->objectTypeID
		);
		$objectAction = new ContentSectionAction(array(
			$this->sectionID
		), 'update', array(
			'data' => $data
		));
		$objectAction->executeAction();
		$this->objectTypeProcessor->saved($this->section);
		
		$this->saved();
		
		WCF::getTPL()->assign('success', true);
	}

	public function assignVariables() {
		parent::assignVariables();
		if ($this->objectType != null) $this->objectTypeProcessor->assignFormVariables();
		WCF::getTPL()->assign(array(
			'sectionID' => $this->sectionID,
			'action' => 'edit',
			'objectType' => $this->objectType,
			'objectTypeName' => $this->objectType->objectType,
			'content' => $this->section->getContent()
		));
	}
}
