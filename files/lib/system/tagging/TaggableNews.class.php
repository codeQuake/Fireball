<?php
namespace cms\system\tagging;
use cms\data\news\TaggedNewsList;
use wcf\data\tag\Tag;
use wcf\system\tagging\ITaggable;

class TaggableNews implements ITaggable{

    public function getObjectList(Tag $tag) {
        return new TaggedNewsList($tag);
    }
    
    public function getTemplateName() {
        return 'newsListing';
    }
    
    public function getApplication() {
        return 'cms';
    }

}
