<?php
namespace cms\data\modification\log;

use cms\system\log\modification\PageModificationLogHandler;
use wcf\data\modification\log\ModificationLogList;
use wcf\system\WCF;

class PageModificationLogList extends ModificationLogList {
	public $objectTypeID = 0;
	public $page = null;

	public function __construct() {
		parent::__construct();
		$objectType = PageModificationLogHandler::getInstance()->getObjectType('de.codequake.cms.page');
		$this->objectTypeID = $objectType->objectTypeID;
	}

	public function setPage($page) {
		$this->page = $page;
	}

	public function countObjects() {
		$sql = "SELECT		COUNT(modification_log.logID) AS count
			    FROM		wcf" . WCF_N . "_modification_log modification_log
			    WHERE		modification_log.objectTypeID = ?
					    AND modification_log.objectID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->objectTypeID,
			$this->page->pageID
		));
		$count = 0;
		while ($row = $statement->fetchArray()) {
			$count += $row['count'];
		}

		return $count;
	}

	public function readObjects() {
		$sql = "SELECT		modification_log.*
			FROM		wcf" . WCF_N . "_modification_log modification_log
			WHERE		modification_log.objectTypeID = ?
					AND modification_log.objectID = ?" . (! empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
		;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->objectTypeID,
			$this->page->pageID
		));
		$this->objects = $statement->fetchObjects(($this->objectClassName ?  : $this->className));
		$objects = array();
		foreach ($this->objects as $object) {
			$objectID = $object->{$this->getDatabaseTableIndexName()};
			$objects[$objectID] = $object;

			$this->indexToObject[] = $objectID;
		}
		$this->objectIDs = $this->indexToObject;
		$this->objects = $objects;
		foreach ($this->objects as &$object) {
			$object = new ViewablePageModificationLog($object);
		}
		unset($object);
	}
}
