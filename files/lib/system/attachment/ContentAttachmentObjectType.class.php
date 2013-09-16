<?php
namespace cms\system\attachment;
use cms\data\content\Content;
use cms\data\content\ContentList;
use wcf\system\attachment\AbstractAttachmentObjectType;
use wcf\system\WCF;

class ContentAttachmentObjectType extends AbstractAttachmentObjectType {
    protected $cachedObjects = array();
    
    
    public function canDownload($objectID) {
        if ($objectID) {
            $content = new Content($objectID);
            if (!$content->isVisible()) return false;
            return WCF::getSession()->getPermission('user.cms.content.canDownloadAttachment');
        }
        return false;
    }
    
    public function canViewPreview($objectID) {
        if ($objectID) {
            $content = new Content($objectID);
            if (!$content->isVisible()) return false;
            return WCF::getSession()->getPermission('user.cms.content.canViewPreview');
        }
        return false;
    }
    
    public function canUpload() {
        return WCF::getSession()->getPermission('admin.content.cms.canUploadAttachment');
    }
    
    public function canDelete($objectID) {
        return (WCF::getSession()->getPermission('admin.content.cms.canEditContent') || WCF::getSession()->getPermission('admin.content.cms.canAddContent'));
    }
    
    public function getObject($objectID) {
        if (isset($this->cachedObjects[$objectID])) return $this->cachedObjects[$objectID];
        return null;
    }
    
    public function cacheObjects(array $objectIDs) {
        $contentList = new ContentList();
        $contentList->setObjectIDs($objectIDs);
        $contentList->readObjects();
        $this->cachedObjects = $contentList->getObjects();
    }
}
