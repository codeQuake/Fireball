<?php
namespace cms\acp\form;

use cms\data\stylesheet\Stylesheet;
use cms\data\stylesheet\StylesheetAction;
use wcf\form\AbstractForm;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class StylesheetEditForm extends StylesheetAddForm {
    public $sheetID = 0;
    public $sheet = null;

    public function readData() {
        parent::readData();
        if (isset($_REQUEST['id'])) $this->sheetID = intval($_REQUEST['id']);
        $this->sheet = new Stylesheet($this->sheetID);
        $this->title = $this->sheet->title;
        $this->less = $this->sheet->less;
    }

    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_REQUEST['id'])) $this->sheetID = intval($_REQUEST['id']);
        $this->sheet = new Stylesheet($this->sheetID);
    }

    public function save() {
        AbstractForm::save();
        $data = array(
            'title' => $this->title,
            'less' => $this->less
        );
        $objectAction = new StylesheetAction(array(
            $this->sheet
        ), 'update', array(
            'data' => $data
        ));
        $objectAction->executeAction();
        
        $this->saved();
        WCF::getTPL()->assign('success', true);
    }

    public function assignVariables() {
        AbstractForm::assignVariables();
        WCF::getTPL()->assign(array(
            'action' => 'edit',
            'title' => $this->title,
            'less' => $this->less,
            'sheetID' => $this->sheetID
        ));
    }
}
