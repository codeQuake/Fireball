<?php
namespace cms\data\content;

class DrainedPositionContentNodeTree extends DrainedContentNodeTree {

	public $position = 'body';

	public function __construct($parentID = null, $pageID = 0, $drainedID = null, $position = 'body', $isACP = 0) {
		$this->drainedID = $drainedID;
		$this->pageID = $pageID;
		$this->parentID = $parentID;
		$this->position = $position;
		$this->isACP = $isACP;
	}

	public function isIncluded(ContentNode $contentNode) {
		if ($contentNode->position != $this->position) return false;
		return parent::isIncluded($contentNode);
	}
}
