<?php
namespace cms\data\stylesheet;

use cms\system\style\StylesheetCompiler;
use wcf\data\DatabaseObject;
use wcf\system\cache\builder\StyleCacheBuilder;
use wcf\system\WCF;

/**
 * Represents a stylesheet.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @property-read	integer		$stylesheetID	id of the stylesheet
 * @property-read	string		$title	    	title of the stylesheet
 * @property-read	string		$scss   		scss code
 */
class Stylesheet extends DatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'stylesheet';

	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'stylesheetID';

	/**
	 * Returns the title of this stylesheet.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return $this->title;
	}

	/**
	 * Compiles this stylesheet.
	 * 
	 * @param	integer		$styleID
	 */
	public function compile($styleID = null) {
		StylesheetCompiler::getInstance()->compile($this, $styleID);
	}

	/**
	 * Returns the physical location of the css file.
	 * 
	 * @param	integer		$styleID
	 * @param	boolean		$rtl
	 * @return	string
	 */
	public function getLocation($styleID, $rtl = false) {
		return CMS_DIR.'style/style-'. $styleID .'-'. $this->stylesheetID . (($rtl) ? '-rtl' : '') .'.css';
	}

	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Returns the url of the compiled css file. The stylesheet will be
	 * compiled automatically in case it doesn't exist yet.
	 * 
	 * @param	integer		$styleID
	 * @param	boolean		$rtl
	 * @return	string
	 */
	public function getURL($styleID = null, $rtl = null) {
		// default values
		if ($styleID === null) {
			$styleID = StyleCacheBuilder::getInstance()->getData(array(), 'default');
		}
		if ($rtl === null) {
			$rtl = (WCF::getLanguage()->get('wcf.global.pageDirection') == 'rtl');
		}

		// compile stylesheet if necessary
		if (!file_exists($this->getLocation($styleID, $rtl))) {
			$this->compile($styleID);
		}

		//return filename with appended caching parameter
		$filename ='style/style-'. $styleID .'-'. $this->stylesheetID . (($rtl) ? '-rtl' : '') .'.css';
		return  WCF::getPath('cms') . $filename.'?m='.filemtime(CMS_DIR.$filename);
	}
}
