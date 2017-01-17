<?php
namespace cms\system\worker;

use cms\data\file\FileAction;
use wcf\system\worker\AbstractRebuildDataWorker;

/**
 * Worker implementation for files.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileRebuildDataWorker extends AbstractRebuildDataWorker {
	/**
	 * @see	\wcf\system\worker\AbstractWorker::$limit
	 */
	protected $limit = 100;

	/**
	 * @see	\wcf\system\worker\AbstractRebuildDataWorker::$objectListClassName
	 */
	protected $objectListClassName = 'cms\data\file\FileList';

	/**
	 * @see	\wcf\system\worker\IWorker::execute()
	 */
	public function execute() {
		parent::execute();

		/** @var \cms\data\file\File $file */
		foreach ($this->objectList->getObjects() as $file) {
			if ($file->isImage() && !$file->hasThumbnail()) {
				$fileAction = new FileAction([$file], 'generateThumbnail');
				$fileAction->executeAction();
			}
		}
	}
}
