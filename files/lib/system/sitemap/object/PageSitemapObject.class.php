<?php

namespace cms\system\sitemap\object;

use cms\data\page\Page;
use wcf\data\DatabaseObject;
use wcf\system\sitemap\object\AbstractSitemapObjectObjectType;

/**
 * Fireball Page sitemap implementation.
 *
 * @author	Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageSitemapObject extends AbstractSitemapObjectObjectType {
	/**
	 * @inheritDoc
	 */
	public function getObjectClass() {
		return Page::class;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getLastModifiedColumn() {
		return 'lastEditTime';
	}
	
	/**
	 * @inheritDoc
	 */
	public function canView(DatabaseObject $object) {
		/** @var Page $object */
		return $object->canRead();
	}
}
