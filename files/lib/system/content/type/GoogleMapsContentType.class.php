<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\WCF;

/**
 * @author	Florian Gail
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class GoogleMapsContentType extends AbstractContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'icon-map-o';

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		return WCF::getTPL()->fetch('googleMapsContentType', 'cms', [
			'latitude' => $content->latitude,
			'longitude' => $content->longitude,
			'title' => $content->getTitle(),
			'contentID' => $content->contentID
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function isAvailableToAdd() {
		return defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY;
	}
}
