<?php
namespace cms\system\clipboard\action;

use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\WCF;

class NewsClipboardAction extends AbstractClipboardAction {
	protected $news = array();
	protected $actionClassActions = array(
		'delete'
	);
	protected $supportedActions = array(
		'delete'
	);

	public function execute(array $objects, ClipboardAction $action) {
		if (empty($this->news)) {
			$this->news = $objects;
		}

		$item = parent::execute($objects, $action);
		if ($item === null) {
			return null;
		}
		switch ($action->actionName) {
			case 'delete':
				$item->addParameter('objectIDs', array_keys($this->news));
				$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.de.codequake.cms.news.delete.confirmMessage', array(
					'count' => $item->getCount()
				)));
				$item->addParameter('className', $this->getClassName());
				$item->setName('de.codequake.cms.news.delete');
				break;
		}
		return $item;
	}

	public function getTypeName() {
		return 'de.codequake.cms.news';
	}

	public function getClassName() {
		return 'cms\data\news\NewsAction';
	}


	protected function validateDelete() {
		$newsIDs = array();
		foreach ($this->news as $news) {
			if ($news->canModerate()) {
				$newsIDs[] = $news->newsID;
			}
		}

		return $newsIDs;
	}
}
