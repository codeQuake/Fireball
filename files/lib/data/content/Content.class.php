<?php
namespace cms\data\content;

use cms\data\content\section\ContentContentSectionList;
use cms\data\page\Page;
use cms\data\CMSDatabaseObject;
use wcf\system\request\IRouteController;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class Content extends CMSDatabaseObject implements IRouteController {
	protected static $databaseTableName = 'content';
	protected static $databaseTableIndexName = 'contentID';
	public $sectionList = array();

	public function __construct($id, $row = null, $object = null) {
		if ($id !== null) {
			$sql = "SELECT *
                    FROM " . static::getDatabaseTableName() . "
                    WHERE (" . static::getDatabaseTableIndexName() . " = ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$id
			));
			$row = $statement->fetchArray();
			
			if ($row === false) $row = array();
		}
		
		parent::__construct(null, $row, $object);
	}

	public function getPage() {
		return new Page($this->pageID);
	}

	public function getEditor() {
		return new ContentEditor($this);
	}

	public function getTitle() {
		return $this->title;
	}

	public function getSections() {
		$this->sectionList = new ContentContentSectionList($this->contentID);
		$this->sectionList->readObjects();
		return $this->sectionList->getObjects();
	}
}
