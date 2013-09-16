<?php
namespace cms\acp\form;

use cms\data\content\ContentAction;
use cms\data\content\ContentEditor;
use cms\system\cache\builder\ContentAttachmentCacheBuilder;
use wcf\system\WCF;
use wcf\form\RecaptchaForm;
use wcf\system\menu\acp\ACPMenu;
use wcf\system\language\I18nHandler;
use wcf\form\MessageForm;


class ContentAddForm extends MessageForm{
    public $templateName = 'contentAdd';
    public $neededPermissions = array(
        'admin.cms.content.canAddContent'
    );
    public $activeMenuItem = 'cms.acp.menu.link.cms.content.add';
    
    public $enableMultilangualism = true;
    public $showSignatureSetting = 0;
    
    public $attachmentObjectType = 'de.codequake.cms.content';
    public $attachmentList = null;
    
    public function readParameters(){
        parent::readParameters();
        I18nHandler::getInstance()->register('subject');
        I18nHandler::getInstance()->register('text');
    }
    
    public function readData(){
        parent::readData();
        $this->attachmentList = ContentAttachmentCacheBuilder::getInstance()->getData(array(), 'attachmentList');
    }
    
    public function readFormParameters(){
        parent::readFormParameters();
        I18nHandler::getInstance()->readValues();
        if (I18nHandler::getInstance()->isPlainValue('subject')) $this->subject = StringUtil::trim(I18nHandler::getInstance()->getValue('subject'));
        if (I18nHandler::getInstance()->isPlainValue('text')) $this->text = MessageUtil::stripCrap(trim(I18nHandler::getInstance()->getValue('text')));
    }
    
    public function validate(){
        parent::validate();
        if (!I18nHandler::getInstance()->isPlainValue('subject')) {
            if (!I18nHandler::getInstance()->validateValue('subject')) {
                throw new UserInputException('subject');
            }
         }
         if (!I18nHandler::getInstance()->isPlainValue('text')) {
            if (!I18nHandler::getInstance()->validateValue('text')) {
                throw new UserInputException('text');
            }
         }
    }
    
    public function save(){
        if (!I18nHandler::getInstance()->isPlainValue('text')) RecaptchaForm::save();
        else parent::save();
        
        $parameters = array(
                        'data' => array(
                            'userID' => WCF::getUser()->userID,
                            'username' => WCF::getUser()->username,
                            'subject' => $this->subject,
                            'message' => $this->text,
                            'enableBBCodes' => $this->enableBBCodes,
                            'enableHtml' => $this->enableHtml,
                            'enableSmilies' => $this->enableSmilies,
                            'time' => TIME_NOW
                            ),
                        'attachmentHandler' => $this->attachmentHandler
                        );
        $this->objectAction = new ContentAction(array(), 'create', $parameters);
        $this->objectAction->executeAction();
        $returnValues = $this->objectAction->getReturnValues();
        $content = $returnValues['returnValues'];
        $contentID = $returnValues['returnValues']->contentID;
        
        $update = array();
        if (!I18nHandler::getInstance()->isPlainValue('subject')) {
            I18nHandler::getInstance()->save('subject', 'cms.content.'.$contentID.'.subject', 'cms.content', PACKAGE_ID);
            $update['subject'] = 'cms.content.'.$contentID.'.subject';
        }
        if (!I18nHandler::getInstance()->isPlainValue('text')) {
            I18nHandler::getInstance()->save('text', 'cms.content.'.$contentID.'.message', 'cms.content', PACKAGE_ID);
            $update['message'] = 'cms.content.'.$contentID.'.message';
        }
        if (!empty($update)) {
            $contentEditor = new ContentEditor($returnValues['returnValues']);
            $contentEditor->update($update);
        }
        
        $this->saved();
        WCF::getTPL()->assign('success', true);
        $this->subject = $this->text = $this->username = '';
        $this->userID = 0;
        I18nHandler::getInstance()->reset();
    }
    
    public function assignVariables(){
        parent::assignVariables();
        I18nHandler::getInstance()->assignVariables();
        
        WCF::getTPL()->assign(array(
                                    'action' => 'add',
                                    'attachmentList' => $this->attachmentList));
    }
}