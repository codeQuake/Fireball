<?php
namespace cms\system\cache\builder;

use wcf\system\cache\builder\AbstractCacheBuilder;
use cms\data\content\ContentList;

class ContentCacheBuilder extends AbstractCacheBuilder{
    protected function rebuild(array $parameters){
        $data = array(
            'contents' => array(),
            'contentIDs' => array()
        );
        $contentList = new ContentList();
        $contentList->readObjects();
        $contents = $contentList->getObjects();
        if (empty($contents)) return $data;
        
        foreach ($contents as $contentID => $content) {
            $data['contents'][$contendID] = $content;
            $data['contentIDs'][] = $contentID;
        }
        return $data;
    }
}