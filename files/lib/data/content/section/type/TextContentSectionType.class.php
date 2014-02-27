<?php
namespace cms\data\content\section\type;
use wcf\system\language\I18nHandler;
use wcf\system\attachment\AttachmentHandler;
use cms\data\content\section\ContentSectionEditor;
use wcf\data\attachment\GroupedAttachmentList;
use wcf\util\MessageUtil;
use wcf\system\bbcode\AttachmentBBCode;
use wcf\system\exception\UserInputException;
use wcf\system\bbcode\BBCodeHandler;
use wcf\data\smiley\SmileyCache;
use wcf\util\StringUtil;
use wcf\util\ArrayUtil;
use cms\data\content\section\ContentSection;
use wcf\system\bbcode\BBCodeParser;
use wcf\system\bbcode\MessageParser;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class TextContentSectionType extends AbstractContentSectionType{

    public $objectType = 'de.codequake.cms.section.type.text';
    public $isMultilingual = true;
    
    //message options
    public $attachmentHandler = null;
    public $attachmentObjectID = 0;
    public $attachmentObjectType = 'de.codequake.cms.content.section';
    public $attachmentParentObjectID = 0;
    public $tmpHash = '';
    public $defaultSmilies = array();
    public $smileyCategories = array();
    public $allowedBBCodesPermission = 'user.message.allowedBBCodes';
    public $enableBBCodes = 1;
    public $enableHtml = 0;
    public $enableSmilies = 1;
    public $permissionCanUseBBCodes = 'user.message.canUseBBCodes';
    public $permissionCanUseHtml = 'user.message.canUseHtml';
    public $permissionCanUseSmilies = 'user.message.canUseSmilies';
    public $showSignature = 0;
    public $showSignatureSetting = 0;
    public $preParse = 0;
    
    public function readParameters(){
        if (isset($_REQUEST['tmpHash'])) {
			$this->tmpHash = $_REQUEST['tmpHash'];
		}
		if (empty($this->tmpHash)) {
			$this->tmpHash = StringUtil::getRandomID();
		}
        if ($this->action != 'add'){            
            $this->attachmentObjectID = intval($_REQUEST['id']);;
        }
        
        if (MODULE_ATTACHMENT && $this->attachmentObjectType) {
			$this->attachmentHandler = new AttachmentHandler($this->attachmentObjectType, $this->attachmentObjectID, $this->tmpHash, $this->attachmentParentObjectID);
		}
        I18nHandler::getInstance()->register('text');
        if (MODULE_SMILEY) {
			$this->smileyCategories = SmileyCache::getInstance()->getCategories();
			foreach ($this->smileyCategories as $index => $category) {
				$category->loadSmilies();
				
				// remove empty categories
				if (!count($category) || $category->isDisabled) {
					unset($this->smileyCategories[$index]);
				}
			}
			
			$firstCategory = reset($this->smileyCategories);
			if ($firstCategory) {
				$this->defaultSmilies = SmileyCache::getInstance()->getCategorySmilies($firstCategory->categoryID ?: null);
			}
		}
		
		if ($this->enableBBCodes && $this->allowedBBCodesPermission) {
			BBCodeHandler::getInstance()->setAllowedBBCodes(explode(',', WCF::getSession()->getPermission($this->allowedBBCodesPermission)));
		}
    }
    
    public function readData($sectionID){
        $section = new ContentSection($sectionID);
        $data = @unserialize($section->additionalData);
        $this->enableSmilies = $data['enableSmilies'];
        $this->enableBBCodes = $data['enableBBCodes'];
        $this->enableHtml = $data['enableHtml'];
        if(isset($data['attachments'])) $this->attachments = $data['attachments'];
        $this->formData['text'] = $section->sectionData;
        I18nHandler::getInstance()->setOptions('text', PACKAGE_ID, $section->sectionData, 'cms.content.section.sectionData\d+');
    }
    
    public function readFormData(){
        I18nHandler::getInstance()->readValues();
        if (I18nHandler::getInstance()->isPlainValue('text')) $this->formData['text'] = MessageUtil::stripCrap(StringUtil::trim(I18nHandler::getInstance()->getValue('text')));
        if (isset($_POST['enableSmilies']) && WCF::getSession()->getPermission($this->permissionCanUseSmilies)) $this->enableSmilies = intval($_POST['enableSmilies']);
        else $this->enableSmilies = 0;
        if (isset($_POST['enableHtml']) && WCF::getSession()->getPermission($this->permissionCanUseHtml)) $this->enableHtml = intval($_POST['enableHtml']);
        else $this->enableHtml = 0;
        if (isset($_POST['enableBBCodes']) && WCF::getSession()->getPermission($this->permissionCanUseBBCodes)) $this->enableBBCodes = intval($_POST['enableBBCodes']);
        else $this->enableBBCodes = 0;
        }
    
    public function validateFormData(){
        if (!I18nHandler::getInstance()->validateValue('text')) {
			if (I18nHandler::getInstance()->isPlainValue('text')) {
				throw new UserInputException('text');
			}
			else {
				throw new UserInputException('text', 'multilingual');
			}
		}
        
    }
    
    public function assignFormVariables(){
        I18nHandler::getInstance()->assignVariables();
        
        
        I18nHandler::getInstance()->assignVariables(!empty($_POST));
        WCF::getTPL()->assign(array('attachmentHandler' => $this->attachmentHandler,
			                        'attachmentObjectID' => $this->attachmentObjectID,
			                        'attachmentObjectType' => $this->attachmentObjectType,
			                        'attachmentParentObjectID' => $this->attachmentParentObjectID,
			                        'tmpHash' => $this->tmpHash,
                                    'defaultSmilies' => $this->defaultSmilies,
			                        'enableBBCodes' => $this->enableBBCodes,
			                        'enableHtml' => $this->enableHtml,
			                        'enableSmilies' => $this->enableSmilies,
                                    'permissionCanUseBBCodes' => $this->permissionCanUseBBCodes,
			                        'permissionCanUseHtml' => $this->permissionCanUseHtml,
			                        'permissionCanUseSmilies' => $this->permissionCanUseSmilies,
                                    'showSignature' => $this->showSignature,
			                        'showSignatureSetting' => $this->showSignatureSetting,
			                        'smileyCategories' => $this->smileyCategories,
                                    'preParse' => $this->preParse));
    }
    
    public function getFormTemplate(){
        return 'textSectionType';
    }
    
    public function saved($section){
        $additionalData = array();
        $additionalData['enableSmilies'] = $this->enableSmilies;
        $additionalData['enableBBCodes'] = $this->enableBBCodes;
        $additionalData['enableHtml'] = $this->enableHtml;
        if($this->attachmentHandler !== null) $additionalData['attachments'] = count($this->attachmentHandler);
        $data = array();
        $data['additionalData'] = serialize($additionalData);
        if(I18nHandler::getInstance()->isPlainValue('text')) {
            $data['sectionData'] = $this->formData['text'];
        }
        $editor = new ContentSectionEditor($section);
        $editor->update($data);
        
        $sectionID = $section->sectionID;
            $update = array();
            if (!I18nHandler::getInstance()->isPlainValue('text')) {
                I18nHandler::getInstance()->save('text', 'cms.content.section.sectionData'.$sectionID, 'cms.content', PACKAGE_ID);
                $update['sectionData'] = 'cms.content.section.sectionData'.$sectionID;
            }
            if (!empty($update)) {
                $editor = new ContentSectionEditor($section);
                $editor->update($update);
            
            }
        if ($this->action == 'add'){
                I18nHandler::getInstance()->reset();
            }
    }
    
    public function getOutput($sectionID){
        $section = new ContentSection($sectionID);
        WCF::getTPL()->assign(array('attachmentList'=> $this->getAttachments($section),
                                    'message' => $this->getFormattedMessage($section),
                                    'objectID' => $sectionID));
        return WCF::getTPL()->fetch('textSectionTypeOutput', 'cms');
    }
    
    public function getFormattedMessage($section) {
        $additionalData = @unserialize($section->additionalData);
        if(!is_array($additionalData)) $additionalData = array();
		AttachmentBBCode::setObjectID($section->sectionID);
		MessageParser::getInstance()->setOutputType('text/html');
		return MessageParser::getInstance()->parse(WCF::getLanguage()->get($section->sectionData), $additionalData['enableSmilies'], $additionalData['enableHtml'], $additionalData['enableBBCodes']);
	}
    
    public function getPreview($sectionID){
        $section = new ContentSection($sectionID);
        return $this->getExcerpt($section);
    }
    
    public function getExcerpt($section, $maxLength = 255 ){
        $additionalData = @unserialize($section->additionalData);
        if(!is_array($additionalData)) $additionalData = array();
        MessageParser::getInstance()->setOutputType('text/simplified-html');
        return WCF::getLanguage()->get(StringUtil::truncateHTML(MessageParser::getInstance()->parse(WCF::getLanguage()->get($section->sectionData), $additionalData['enableSmilies'], $additionalData['enableHtml'], $additionalData['enableBBCodes']), $maxLength));
    }
    
    public function getAttachments($section) {
        $additionalData = @unserialize($section->additionalData);
        if(!is_array($additionalData)) $additionalData = array();
		if (MODULE_ATTACHMENT == 1 && $additionalData['attachments']) {
			$attachmentList = new GroupedAttachmentList('de.codequake.cms.content.section');
			$attachmentList->getConditionBuilder()->add('attachment.objectID IN (?)', array($section->sectionID));
			$attachmentList->readObjects();
			
			// set embedded attachments
			AttachmentBBCode::setAttachmentList($attachmentList);
			
			return $attachmentList;
		}
		
		return null;
	}
}
