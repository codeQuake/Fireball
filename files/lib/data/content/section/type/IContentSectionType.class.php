<?php
namespace cms\data\content\section\type;

interface IContentSectionType{
    
    public $formData = array();
    
    //reads form data 
    public function readFormData(){ }
    
    //validate form data
    public function validateFormData(){ }
    
    //saving form data
    public function saveFormData(){ }
    
    //assigns variables
    public function assignFormVariables(){ }
    
    //provides an individual template for each type
    public function getFormTemplate(){ }
}