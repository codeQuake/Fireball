<?php
namespace cms\data\news;

use wcf\data\tag\Tag;
use wcf\system\tagging\TagEngine;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TaggedNewsList extends ViewableNewsList {

	public function __construct(Tag $tag) {
		parent::__construct();
		$this->getConditionBuilder()->add('tag_to_object.objectTypeID = ? AND tag_to_object.languageID = ? AND tag_to_object.tagID = ?', array(
			TagEngine::getInstance()->getObjectTypeID('de.codequake.cms.news'),
			$tag->languageID,
			$tag->tagID
		));
		$this->getConditionBuilder()->add('news.newsID = tag_to_object.objectID');
	}

	public function countObjects() {
		$sql = "SELECT	COUNT(*) AS count
            FROM	wcf" . WCF_N . "_tag_to_object tag_to_object,
                cms" . WCF_N . "_news news
            " . $this->sqlConditionJoins . "
            " . $this->getConditionBuilder();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()
			->getParameters());
		$row = $statement->fetchArray();
		return $row['count'];
	}

	public function readObjectIDs() {
		$this->objectIDs = array();
		$sql = "SELECT	tag_to_object.objectID
            FROM	wcf" . WCF_N . "_tag_to_object tag_to_object,
                cms" . WCF_N . "_news news
                " . $this->sqlConditionJoins . "
                " . $this->getConditionBuilder() . "
                " . (! empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
		$statement = WCF::getDB()->prepareStatement($sql, $this->sqlLimit, $this->sqlOffset);
		$statement->execute($this->getConditionBuilder()
			->getParameters());
		while ($row = $statement->fetchArray()) {
			$this->objectIDs[] = $row['objectID'];
		}
	}

	public function readObjects() {
		if ($this->objectIDs === null) $this->readObjectIDs();
		parent::readObjects();
	}
}
