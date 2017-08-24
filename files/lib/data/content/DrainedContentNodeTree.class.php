<?php
namespace cms\data\content;

class DrainedContentNodeTree extends ContentNodeTree {

	public $drainedID = null;

	public function __construct($parentID = null, $pageID = 0, $drainedID = null) {
		parent::__construct($parentID, $pageID);
		
		$this->drainedID = $drainedID;
	}

	public function isIncluded(ContentNode $contentNode) {
		if ($this->drainedID != null && $contentNode->contentID == $this->drainedID) return false;
		return parent::isIncluded($contentNode);
	}
}
