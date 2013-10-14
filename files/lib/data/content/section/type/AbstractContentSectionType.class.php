<?php
namespace cms\data\content\section\type;

class AbstractContentSectionType implements IContentSectionType{
    
    public $isMultilingual = false;
    public $formData = array();
    public $objectType = "";
    
    //reads parameters at the beginning
    public function readParameters(){ }
    
    //reads form data 
    public function readFormData(){ }
    
    //validate form data
    public function validateFormData(){ }
    
    //saving form data
    public function saveFormData(){ }
    
    //assigns variables
    public function assignFormVariables(){ }
    
    //provides an individual template for each type
    public function getFormTemplate() { }
    
    public function getFormData() {
		return $this->formData;
	}
    
    //after saving, it works with the returnvalues
    public function saved($returnValues){ }
    
    public function getOutput($sectionID){
        return '';
    }
    
    public function getPreview($sectionID){
        return '###'.$this->objectType.'###';
    }
}