<?php
namespace cms\data\content\section\type;

interface IContentSectionType{
    
    //reads form parameters before saving
    public function readParameters();
    
    //reads data for edits
    public function readData($sectionID);
    
    //reads form data 
    public function readFormData();
    
    //validate form data
    public function validateFormData();
    
    //saving form data
    public function saveFormData();
    
    //assigns variables
    public function assignFormVariables();
    
    //provides an individual template for each type
    public function getFormTemplate();
    
    //gets formdata
    public function getFormData();
    
    //after saving, it works with the returnvalues
    public function saved($section);
    
    //gets the Output
    public function getOutput($sectionID);
    
    //preview
    public function getPreview($sectionID);
    
    //add or edit?
    public function setAction($action);
}