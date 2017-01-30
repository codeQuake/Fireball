<?php

namespace cms\system\importer;
use wcf\system\importer\AbstractCommentResponseImporter;

/**
 * Provides an importer for comment responses
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageCommentResponseImporter extends AbstractCommentResponseImporter {
	/**
	 * @inheritDoc
	 */
	protected $objectTypeName = 'de.codequake.cms.page.comment';
}
