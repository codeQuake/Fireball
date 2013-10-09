<?php
namespace cms\data\content\section\type;

class AbstractContentSectionType implements IContentSectionType{
    
    public $isMultilingual = false;
    public $formData = array();
    public $objectType = "";
    
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
}