<?php
namespace cms\data\page;

use wcf\data\DatabaseObjectDecorator;

/**
 * Represents a viewable page.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 *
 * @mixin Page
 * @method Page getDecoratedObject()
 */
class ViewablePage extends DatabaseObjectDecorator {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = Page::class;
}
