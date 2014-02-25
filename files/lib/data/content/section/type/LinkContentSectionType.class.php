<?php
namespace cms\data\content\section\type;
use wcf\system\language\I18nHandler;
use cms\data\content\section\ContentSectionEditor;
use wcf\util\StringUtil;
use cms\data\content\section\ContentSection;
use wcf\system\WCF;
use wcf\system\exception\UserInputException;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class LinkContentSectionType extends AbstractContentSectionType {

    public $objectType = 'de.codequake.cms.section.type.link';
    public $isMultilingual = true;
    public $type = '';
    public $hyperlink = '';
    public $additionalData = array();

    public function readParameters() {
        I18nHandler::getInstance()->register('sectionData');
    }

    public function readData($sectionID) {
        $section = new ContentSection($sectionID);
        $data = @unserialize($section->additionalData);
        $this->type = isset($data['type']) ? $data['type'] : '';
        $this->hyperlink = isset($data['hyperlink']) ? $data['hyperlink'] : '';
        I18nHandler::getInstance()->setOptions('sectionData', PACKAGE_ID, $section->sectionData, 'cms.content.section.sectionData\d+');
        $this->formData['sectionData'] = $section->sectionData;
    }

    public function readFormData() {        
        if (isset($_POST['type'])) $this->type = StringUtil::trim($_POST['type']);
        if (isset($_POST['hyperlink'])) $this->hyperlink = StringUtil::trim($_POST['hyperlink']);
        I18nHandler::getInstance()->readValues();
        if (I18nHandler::getInstance()->isPlainValue('sectionData'))
            $this->formData['sectionData'] = StringUtil::trim(I18nHandler::getInstance()->getValue('sectionData'));
    }

    public function validateFormData() {
        if (!I18nHandler::getInstance()->validateValue('sectionData')) {
            if (I18nHandler::getInstance()->isPlainValue('sectionData')) {
                throw new UserInputException('sectionData');
            } else {
                throw new UserInputException('sectionData', 'multilingual');
            }
        }
    }

    public function assignFormVariables() {
        I18nHandler::getInstance()->assignVariables();

        I18nHandler::getInstance()->assignVariables(!empty($_POST));
        WCF::getTPL()->assign(array('type' => $this->type,
                                    'hyperlink' => $this->hyperlink));
        
    }

    public function getFormTemplate() {
        return 'linkSectionType';
    }

    public function saved($section) {
        $additionalData = array();
        $additionalData['type'] = $this->type;
        $additionalData['hyperlink'] = $this->hyperlink;
        $data = array();
        $data['additionalData'] = serialize($additionalData);
        if (I18nHandler::getInstance()->isPlainValue('sectionData')) {
            $data['sectionData'] = $this->formData['sectionData'];
            
        }
        $editor = new ContentSectionEditor($section);
        $editor->update($data);

        $sectionID = $section->sectionID;
        $update = array();
        if (!I18nHandler::getInstance()->isPlainValue('sectionData')) {
            I18nHandler::getInstance()->save('sectionData', 'cms.content.section.sectionData' . $sectionID, 'cms.content', PACKAGE_ID);
            $update['sectionData'] = 'cms.content.section.sectionData' . $sectionID;
        }
        if (!empty($update)) {
            $editor = new ContentSectionEditor($section);
            $editor->update($update);
        }
        if ($this->action == 'add') {
            I18nHandler::getInstance()->reset();
            $this->additionalData = '';
            $this->hyperlink = '';
            $this->type = 0;
        }
    }

    public function getOutput($sectionID) {
        $section = new ContentSection($sectionID);
        $additionalData = @unserialize($section->additionalData);
        WCF::getTPL()->assign(array('sectionData' => $section->sectionData,
                                    'type' => $additionalData['type'],
                                    'hyperlink' => isset($additionalData['hyperlink']) ? $additionalData['hyperlink'] : ''));
        return WCF::getTPL()->fetch('linkSectionTypeOutput', 'cms');
    }

    public function getPreview($sectionID) {
        $section = new ContentSection($sectionID);
        $additionalData = @unserialize($section->additionalData);
        if (!is_array($additionalData))
            $additionalData = array();
        if (preg_match('/cms.content./', $section->sectionData))
            return WCF::getLanguage()->get('cms.acp.content.section.type.de.codequake.cms.section.type.link') . ' ' . $additionalData['type'] . ' -> ' . WCF::getLanguage()->get($section->sectionData);
        return WCF::getLanguage()->get('cms.acp.content.section.type.de.codequake.cms.section.type.link') . ' ' . $additionalData['type'] . ' -> ' . $section->sectionData;
    }

}