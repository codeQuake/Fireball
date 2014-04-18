<?php
namespace cms\acp\form;

use cms\data\content\section\ContentSectionAction;
use cms\data\content\Content;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractForm;
use wcf\system\event\EventHandler;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class ContentSectionAddForm extends AbstractForm {
    public $templateName = 'sectionAdd';
    public $neededPermissions = array(
        'admin.cms.content.canAddContentSection'
    );
    public $activeMenuItem = 'cms.acp.menu.link.cms.content.add';
    public $enableMultilangualism = true;
    public $objectType = null;
    public $objectTypeList = array();
    public $objectTypeProcessor = null;
    public $contentID = 0;
    public $showOrder = 0;
    public $cssID = '';
    public $cssClasses = '';
    public $send = false;

    public function readParameters()
    {
        parent::readParameters();
        // getObjectTypeByName($definitionName, $objectTypeName);
        if (isset($_REQUEST['objectType'])) $this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.section.type', $_REQUEST['objectType']);
        if ($this->objectType != null) {
            $this->objectTypeProcessor = $this->objectType->getProcessor();
            $this->objectTypeProcessor->setAction('add');
            $this->objectTypeProcessor->readParameters();
        }
    }

    public function readData()
    {
        parent::readData();
        $this->objectTypeList = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.cms.section.type');
        if (isset($_GET['id'])) $this->contentID = intval($_GET['id']);
    }

    public function readFormParameters()
    {
        parent::readFormParameters();
        
        if (isset($_REQUEST['objectType'])) $this->objectType = ObjectTypeCache::getInstance()->getObjectTypeByName('de.codequake.cms.section.type', $_REQUEST['objectType']);
        if ($this->objectType != null) $this->objectTypeProcessor = $this->objectType->getProcessor();
        if (isset($_POST['send'])) $this->send = (boolean) $_POST['send'];
        if (isset($_POST['id'])) $this->contentID = intval($_POST['id']);
        if (isset($_POST['showOrder'])) $this->showOrder = intval($_POST['showOrder']);
        if (isset($_POST['cssClasses'])) $this->cssClasses = StringUtil::trim($_POST['cssClasses']);
        if (isset($_POST['cssID'])) $this->cssID = StringUtil::trim($_POST['cssID']);
    }

    public function validate()
    {
        parent::validate();
        if ($this->objectType != null) $this->objectTypeProcessor->validateFormData();
        else
            throw new UserInputException('objectType');
    }

    public function submit()
    {
        // call submit event
        EventHandler::getInstance()->fireAction($this, 'submit');
        
        $this->readFormParameters();
        
        try {
            if ($this->send) {
                if ($this->objectType != null) $this->objectTypeProcessor->readFormData();
                $this->validate();
                // no errors
                $this->save();
            }
        }
        catch (UserInputException $e) {
            $this->errorField = $e->getField();
            $this->errorType = $e->getType();
        }
    }

    public function save()
    {
        parent::save();
        $this->objectTypeProcessor->saveFormData();
        
        $data = array(
            'contentID' => $this->contentID,
            'showOrder' => $this->showOrder,
            'cssID' => $this->cssID,
            'cssClasses' => $this->cssClasses,
            'sectionTypeID' => $this->objectType->objectTypeID
        );
        $objectAction = new ContentSectionAction(array(), 'create', array(
            'data' => $data
        ));
        $objectAction->executeAction();
        $returnValues = $objectAction->getReturnValues();
        
        $this->objectTypeProcessor->saved($returnValues['returnValues']);
        
        $this->saved();
        
        WCF::getTPL()->assign('success', true);
        
        $this->showOrder = 0;
        $this->cssID = $this->cssClasses = '';
    }

    public function assignVariables()
    {
        parent::assignVariables();
        
        if ($this->objectType != null) $this->objectTypeProcessor->assignFormVariables();
        
        WCF::getTPL()->assign(array(
            'action' => 'add',
            'cssID' => $this->cssID,
            'cssClasses' => $this->cssClasses,
            'contentID' => $this->contentID,
            'content' => new Content($this->contentID),
            'showOrder' => $this->showOrder,
            'objectTypeName' => isset($this->objectType->objectType) ? $this->objectType->objectType : '',
            'objectType' => isset($this->objectType) ? $this->objectType : null,
            'objectTypeList' => $this->objectTypeList
        ));
    }
}
