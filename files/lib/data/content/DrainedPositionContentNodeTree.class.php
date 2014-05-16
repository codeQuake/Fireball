<?php
namespace cms\data\content;

class DrainedPositionContentNodeTree extends DrainedContentNodeTree {
	public $position = 'body';

	public function __construct($parentID = null, $pageID = 0, $drainedID = null, $position = 'body') {
		$this->drainedID = $drainedID;
		$this->pageID = $pageID;
		$this->parentID = $parentID;
		$this->position = $position;
	}

	public function isIncluded(ContentNode $contentNode) {
		if ($contentNode->position != $this->position) return false;
		return parent::isIncluded($contentNode);
	}
}
