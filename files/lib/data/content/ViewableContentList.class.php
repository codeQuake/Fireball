<?php
namespace cms\data\content;

use cms\data\content\ContentList;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class ViewableContentList extends ContentList {
    public $decoratorClassName = 'cms\data\content\ViewableContent';
}
