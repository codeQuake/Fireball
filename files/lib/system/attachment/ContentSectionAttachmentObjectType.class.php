<?php
namespace cms\system\attachment;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use cms\data\content\section\ContentSection;
use wcf\system\attachment\AbstractAttachmentObjectType;

class ContentSectionAttachmentObjectType extends AbstractAttachmentObjectType {

    public function getMaxSize() {
            return WCF::getSession()->getPermission('admin.cms.content.attachmentMaxSize');
    }
    
    public function getAllowedExtensions() {
        return ArrayUtil::trim(explode("\n", WCF::getSession()->getPermission('admin.cms.content.allowedAttachmentExtensions')));
    }
    
    public function getMaxCount() {
        return WCF::getSession()->getPermission('admin.cms.content.maxAttachmentCount');
    }
    
    public function canDownload($objectID) {
	    return WCF::getSession()->getPermission('user.cms.content.canDownloadAttachments');
    }
    
    public function canUpload($objectID, $parentObjectID = 0) {		
		return WCF::getSession()->getPermission('admin.cms.content.canUploadAttachment');
	}
    
    public function canDelete($objectID) {
        return WCF::getSession()->getPermission('admin.cms.content.canUploadAttachment');
	}
}