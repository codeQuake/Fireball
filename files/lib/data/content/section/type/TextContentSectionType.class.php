<?php
namespace cms\data\content\section\type;
use wcf\util\StringUtil;

class TextContentSectionType implements IContentSectionType{

    public function readFormData(){
        if(isset($_POST['text'])) $this->formData['text'] = StringUtil::trim($_POST['text']);
    }
    
    public function validateFormData(){ }
    
    public function saveFormData(){ }
    
    public function assignFormVariables(){ }
    
    public function getFormTemplate(){
        return 'textSectionType';
    }
}