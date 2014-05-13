<?php
namespace cms\system\content\type;

use cms\data\content\Content;

/**
 * Interface for Basic Contenttypes
 * 
 * @author Jens Krumsieck
 * @copyright codeQuake 2014
 * @package de.codequake.cms
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */
interface IContentType {
	// validates form data
	public function validate($data);
	
	// get Output
	public function getOutput(Content $content);
	
	// returns type's icon
	public function getIcon();
	
	// returns template name
	public function getFormTemplate();
}
