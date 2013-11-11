<?php
namespace cms\data\news;
use wcf\data\search\ISearchResultObject;
use wcf\system\search\SearchResultTextParser;
use wcf\system\request\LinkHandler;

class SearchResultNews extends ViewableNews implements ISearchResultObject {

	public function getFormattedMessage() {
		return SearchResultTextParser::getInstance()->parse($this->getDecoratedObject()->getSimplifiedFormattedMessage());
	}

	public function getSubject() {
		return $this->subject;
	}

	public function getLink($query = '') {
        if($query){
            return LinkHandler::getInstance()->getLink('Link', array('application' => 'cms',
                                                                    'object' => $this->getDecoratedObject(),
                                                                    'highlight' => urlencode($query)));
        }
		return $this->getDecoratedObject()->getLink();
	}

	public function getTime() {
		return $this->time;
	}
	

	public function getObjectTypeName() {
		return 'de.codequake.cms.news';
	}

	public function getContainerTitle() {
		return '';
	}
	
	public function getContainerLink() {
		return '';
	}
    
    public function getUserProfile(){
        return $this->getDecoratedObject()->getUserProfile();
    }
}
