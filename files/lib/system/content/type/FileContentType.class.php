<?php
namespace cms\system\content\type;
use cms\data\file\FileList;
use cms\data\folder\FolderList;
use wcf\system\WCF;

/**
 * File content type implementation.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class FileContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-file';

	/**
	 * @see	\cms\system\content\type\IContentType::isAvailableToAdd()
	 */
	public function isAvailableToAdd() {
		$fileList = new FileList();
		$count = $fileList->countObjects();

		return $count > 0;
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getFormOutput()
	 */
	public function getFormOutput() {
		$list = new FileList();
		$list->getConditionBuilder()->add('file.folderID =  ?', array(0));
		$list->readObjects();
		$rootList = $list->getObjects();

		$list = new FolderList();
		$list->readObjects();
		$folderList = $list->getObjects();

		WCF::getTPL()->assign(array(
			'rootList' => $rootList,
			'folderList' => $folderList
		));

		return parent::getFormOutput();
	}
}
