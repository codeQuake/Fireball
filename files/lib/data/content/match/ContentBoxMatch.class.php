<?php

namespace cms\data\content\match;

use cms\data\content\Content;
use cms\data\content\ContentCache;
use wcf\data\dashboard\box\DashboardBox;
use wcf\data\DatabaseObject;

/**
 * @author	Florian Gail
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class ContentBoxMatch extends DatabaseObject {
	/**
	 * @see DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'content_to_dashboardbox';

	/**
	 * @see DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'boxID';

	/**
	 * Returns the content object
	 *
	 * @return Content
	 * @throws \wcf\system\exception\SystemException
	 */
	public function getContent() {
		$content = ContentCache::getInstance()->getContent($this->contentID);
		if ($content !== null)
			return $content;

		return new Content($this->contentID);
	}

	/**
	 * Returns the dashboard box object
	 * @return DashboardBox
	 */
	public function getBox() {
		return new DashboardBox($this->boxID);
	}
}
