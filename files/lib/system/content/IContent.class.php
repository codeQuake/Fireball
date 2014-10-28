<?php
namespace cms\system\content;
use wcf\data\IDatabaseObjectProcessor;

/**
 * Every content processor has to implement this interface. A content processor
 * provides information for one specific content and handles the output of that
 * content.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
interface IContent extends IDatabaseObjectProcessor {
	/**
	 * Reads the parameters that affect the represented content.
	 */
	public function readParameters();

	/**
	 * Reads/Gets the data related to the represented content.
	 */
	public function readData();

	/**
	 * Returns the rendered output to display the represented content.
	 * 
	 * @return	string
	 */
	public function getOutput();

	/**
	 * Sets options for the multilingual inputs used in the page edit
	 * formular.
	 */
	public function setI18nOptions();
}
