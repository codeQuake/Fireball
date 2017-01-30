<?php

namespace cms\page;

/**
 * Shows a created page.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
interface ICMSPage {
	/**
	 * Returns the page that belongs to the displayed page.
	 *
	 * @return	\cms\data\page\Page
	 */
	public function getPage();
}
