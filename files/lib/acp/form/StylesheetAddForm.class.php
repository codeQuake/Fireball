<?php
namespace cms\acp\form;

use cms\data\stylesheet\StylesheetAction;
use wcf\form\AbstractForm;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class StylesheetAddForm extends AbstractForm {
    public $templateName = 'stylesheetAdd';
    public $neededPermissions = array(
        'admin.cms.style.canAddStylesheet'
    );
    public $activeMenuItem = 'cms.acp.menu.link.cms.stylesheet.add';
    public $title = '';
    public $less = '';

    public function readFormParameters()
    {
        parent::readFormParameters();
        if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
        if (isset($_POST['less'])) $this->less = StringUtil::trim($_POST['less']);
    }

    public function save()
    {
        parent::save();
        
        $data = array(
            'title' => $this->title,
            'less' => $this->less
        );
        $objectAction = new StylesheetAction(array(), 'create', array(
            'data' => $data
        ));
        $objectAction->executeAction();
        
        $this->saved();
        WCF::getTPL()->assign('success', true);
        
        $this->title = $this->less = '';
    }

    public function assignVariables()
    {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
            'action' => 'add',
            'title' => $this->title,
            'less' => $this->less
        ));
    }
}
