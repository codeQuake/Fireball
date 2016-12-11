<?php
namespace cms\data\page\revision;
use wcf\data\DatabaseObject;

/**
 * Represents a page revision.
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @property-read	integer		$revisionID		id of the revision
 * @property-read	integer		$pageID			id of the page
 * @property-read	integer		$action			action which has been performed
 * @property-read	integer		$userID			id of the user
 * @property-read	string		$username		username of the user
 * @property-read	integer		$time			timestamp
 * @property-read	string		$data			data of the revision
 * @property-read	array		$contentData	content data of the revision
 */
class PageRevision extends DatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'page_revision';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'revisionID';
}
