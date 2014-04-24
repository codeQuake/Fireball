<?php
namespace cms\system\tagging;

use cms\data\news\TaggedNewsList;
use wcf\data\tag\Tag;
use wcf\system\tagging\ITaggable;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class TaggableNews implements ITaggable {

	public function getObjectList(Tag $tag) {
		return new TaggedNewsList($tag);
	}

	public function getTemplateName() {
		return 'newsListing';
	}

	public function getApplication() {
		return 'cms';
	}
}
