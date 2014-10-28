<?php
namespace cms\system\content\type;
use wcf\data\IDatabaseObjectProcessor;

/**
 * Every content type has to implement this interface.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
interface IContentType extends IDatabaseObjectProcessor {
	/**
	 * Returns the name of the content form class. The form class is in
	 * charge of handling the form inputs and save them to the database.
	 * 
	 * @return	string
	 */
	public static function getContentFormClass();

	/**
	 * Returns the name of the content processor class.
	 * 
	 * @return	string
	 */
	public static function getContentProcessorClass();

	/**
	 * Returns the form output used for adding/editing page contents.
	 * 
	 * @return	string
	 */
	public function getFormOutput();

	/**
	 * Returns the icon name (with icon prefix) for this content type.
	 * 
	 * @return	string
	 */
	public function getIcon();

	/**
	 * Returns whether it's currently possible to create a content of this
	 * type.
	 * 
	 * @return	boolean
	 */
	public function isAvailableToAdd();
}
