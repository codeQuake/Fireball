<?php
namespace cms\system\cache\builder;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\data\attachment\GroupedAttachmentList;

class ContentAttachmentCacheBuilder extends AbstractCacheBuilder{

    protected function rebuild(array $parameters) {
        $data = array(
            'attachmentList' => null
        );
        $contents = ContentCacheBuilder::getInstance()->getData(array(), 'contents');
        $attachmentObjectIDs = array();
        
        foreach ($contents as $contentID => $content) {
            if ($content->__get('attachments')) {
                $attachmentObjectIDs[] = $contentID;
            }
        }
        $attachmentList = new GroupedAttachmentList('de.codequake.cms.content');
        if (!empty($attachmentObjectIDs)) {
            $attachmentList->getConditionBuilder()->add('attachment.objectID IN (?)', array($attachmentObjectIDs));
        }
        $attachmentList->readObjects();
        $data['attachmentList'] = $attachmentList;
        return $data;
    }
}