<?php
namespace cms\system\attachment;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use cms\data\news\News;
use wcf\system\attachment\AbstractAttachmentObjectType;

class NewsSectionAttachmentObjectType extends AbstractAttachmentObjectType {

    public function getMaxSize() {
            return WCF::getSession()->getPermission('user.cms.news.attachmentMaxSize');
    }
    
    public function getAllowedExtensions() {
        return ArrayUtil::trim(explode("\n", WCF::getSession()->getPermission('user.cms.news.allowedAttachmentExtensions')));
    }
    
    public function getMaxCount() {
        return WCF::getSession()->getPermission('user.cms.news.maxAttachmentCount');
    }
    
    public function canDownload($objectID) {
	    return WCF::getSession()->getPermission('user.cms.news.canDownloadAttachments');
    }
    
    public function canViewPreview($objectID) {
		return WCF::getSession()->getPermission('user.cms.news.canDownloadAttachments');
	}
    
    public function canUpload($objectID, $parentObjectID = 0) {		
		return WCF::getSession()->getPermission('user.cms.news.canUploadAttachment');
	}
    
    public function canDelete($objectID) {
        return WCF::getSession()->getPermission('user.cms.news.canUploadAttachment');
	}
}