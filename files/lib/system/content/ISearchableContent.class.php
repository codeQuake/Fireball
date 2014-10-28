<?php
namespace cms\system;

/**
 * Every content that is relevant for the page search have to implement this
 * interface.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
interface ISearchableContent {
	/**
	 * Returns relevant information for the search index. The returned
	 * array should contain the information for all available languages.
	 * As key, the respective language id must be used. In case a language
	 * is missing within the returned array, the search index won't
	 * contain information for this content in that language.
	 * 
	 * @return	array<string>
	 */
	public function getSearchIndexData();
}
