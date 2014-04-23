<?php
namespace cms\acp\action;

use cms\data\restore\RestoreAction;
use cms\system\export\CMSExportHandler;
use wcf\action\AbstractAction;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms.exporter
 */
class CMSExportAction extends AbstractAction {
	public function execute() {
		parent::execute();
		$filename = CMSExportHandler::getInstance()->getExportArchive();
		$data = array(
			'filename' => $filename,
			'time' => TIME_NOW
		);
		$action = new RestoreAction(array(), 'create', array(
			'data' => $data
		));
		$action->executeAction();
		
		$this->executed();
		// headers for downloading file
		header('Content-Type: application/x-gzip; charset=utf8');
		header('Content-Disposition: attachment; filename="CMS-Export.tar.gz"');
		readfile($filename);
	}
}
