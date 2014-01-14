<?php
namespace cms\data\content\section\type;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */

class AbstractContentSectionType implements IContentSectionType{
    
    public $isMultilingual = false;
    public $formData = array();
    public $action;
    public $objectType = "";
    
    //reads parameters at the beginning
    public function readParameters(){ }
    
    //reads data for edits
    public function readData($sectionID){ }
    
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
    public function saved($section){ }
    
    public function getOutput($sectionID){
        return '';
    }
    
    public function getPreview($sectionID){
        return '###'.$this->objectType.'###';
    }
    
    public function setAction($action){
        $this->action = $action;
    }
}