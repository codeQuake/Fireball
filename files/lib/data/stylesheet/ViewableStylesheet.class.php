<?php
namespace cms\data\stylesheet;

use wcf\data\DatabaseObjectDecorator;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class ViewableStylesheet extends DatabaseObjectDecorator {
	protected static $baseClass = 'cms\data\stylesheet\Stylesheet';
}
