<?php
namespace cms\system\content\type;

/**
 * Interface for searchable Contenttypes
 *
 * @author Jens Krumsieck
 * @copyright codeQuake 2014
 * @package de.codequake.cms
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 */

interface ISearchableContentType {

	/**
	 * Return data for the search index
	 *
	 * @return	array
	 */
	public function getSearchableData();

}
