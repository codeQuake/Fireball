<?php
namespace cms\system\attachment;

use cms\data\content\section\ContentSection;
use wcf\system\attachment\AbstractAttachmentObjectType;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
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

	public function canViewPreview($objectID) {
		return WCF::getSession()->getPermission('user.cms.content.canDownloadAttachments');
	}

	public function canUpload($objectID, $parentObjectID = 0) {
		return WCF::getSession()->getPermission('admin.cms.content.canUploadAttachment');
	}

	public function canDelete($objectID) {
		return WCF::getSession()->getPermission('admin.cms.content.canUploadAttachment');
	}
}
