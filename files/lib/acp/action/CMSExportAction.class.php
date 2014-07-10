<?php
namespace cms\acp\action;


use cms\system\backup\BackupHandler;
use wcf\action\AbstractAction;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

class CMSExportAction extends AbstractAction {

	public function execute() {
		parent::execute();
		$filename = BackupHandler::getInstance()->getExportArchive();
		$this->executed();

		header('Content-Type: application/x-gzip; charset=utf8');
		header('Content-Disposition: attachment; filename="CMS-Export.tar.gz"');
		readfile($filename);
		@unlink($filename);
	}
}
