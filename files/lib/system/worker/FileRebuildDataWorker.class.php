<?php
namespace cms\system\worker;

use cms\data\file\FileAction;
use cms\data\file\FileList;
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
	 * @inheritDoc
	 */
	protected $limit = 100;

	/**
	 * @inheritDoc
	 */
	protected $objectListClassName = FileList::class;

	/**
	 * @inheritDoc
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
