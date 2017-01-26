<?php
namespace cms\system\stat;
use wcf\system\stat\AbstractCommentStatDailyHandler;

/**
 * Stat handler implementation for page comments.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 Florian Frantzen
 * @license	GNU General Public License <http://opensource.org/licenses/GPL-3.0>
 * @package	de.codequake.cms
 */
class PageCommentStatDailyHandler extends AbstractCommentStatDailyHandler {
	/**
	 * @inheritDoc
	 */
	protected $objectType = 'de.codequake.cms.page.comment';
}
