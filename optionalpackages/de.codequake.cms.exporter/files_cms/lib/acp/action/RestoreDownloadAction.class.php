<?php
namespace cms\acp\action;

use cms\data\restore\Restore;
use wcf\action\AbstractAction;
use wcf\system\exception\IllegalLinkException;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms.exporter
 */
class RestoreDownloadAction extends AbstractAction {
	public $restoreID = 0;

	public function readParameters() {
		parent::readParameters();
		if (isset($_REQUEST['id'])) $this->restoreID = intval($_REQUEST['id']);
	}

	public function execute() {
		parent::execute();
		$restore = new Restore($this->restoreID);
		if ($restore->restoreID == 0) throw new IllegalLinkException();
		$filename = $restore->filename;
		$this->executed();
		// headers for downloading file
		header('Content-Type: application/x-gzip; charset=utf8');
		header('Content-Disposition: attachment; filename="CMS-Export.tar.gz"');
		readfile($filename);
	}
}
