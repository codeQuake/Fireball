<?php
namespace cms\acp\form;

use cms\system\export\CMSImportHandler;
use wcf\form\AbstractForm;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms.exporter
 */
class ImportForm extends AbstractForm {
    public $templateName = 'import';
    public $neededPermissions = array(
        'admin.cms.restore.canRestore'
    );
    public $activeMenuItem = 'cms.acp.menu.link.cms.restore.list';
    public $file = null;

    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_FILES['file'])) $this->file = $_FILES['file'];
    }

    public function validate() {
        parent::validate();
        // check if file is given
        if (empty($this->file)) {
            throw new UserInputException('file', 'empty');
        }
        if (empty($this->file['tmp_name'])) throw new UserInputException('file', 'empty');
        if ($this->file['size'] >= $this->return_bytes(ini_get('upload_max_filesize'))) throw new UserInputException('file', 'tooBig');
        if ($this->file['size'] >= $this->return_bytes(ini_get('post_max_size'))) throw new UserInputException('file', 'tooBig');
    }

    public function save() {
        parent::save();
        CMSImportHandler::getInstance()->handleImport($this->file['tmp_name']);
        $this->saved();
        WCF::getTPL()->assign('success', true);
    }
    
    // see http://www.php.net/manual/de/function.ini-get.php
    function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        
        return $val;
    }
}
