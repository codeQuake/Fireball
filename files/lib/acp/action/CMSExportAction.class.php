<?php
namespace cms\acp\action;

use cms\system\backup\BackupHandler;
use wcf\action\AbstractAction;

/**
 * Exports the cms data as gzip archive.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class CMSExportAction extends AbstractAction {
	/**
	 * @see	\wcf\action\IAction::execute()
	 */
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
