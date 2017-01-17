<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\data\box\Box;
use wcf\data\box\BoxList;
use wcf\system\WCF;

/**
 * @author	Florian Gail
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class BoxContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'fa-dashboard';

	/**
	 * @see	\cms\system\content\type\IContentType::getFormTemplate()
	 */
	public function getFormTemplate() {
		$boxList = new BoxList();
		$boxList->readObjects();
		$boxList = $boxList->getObjects();

		$boxesByPosition = [
			'content' => [],
			'sidebarLeft' => [],
			'sidebarRight' => []
		];

		foreach ($boxList as $box) {
			$controller = $box->getController();
			if ($box->boxType == 'menu' || ($controller === null && $box->boxType == 'system')) {
				continue;
			} else if ($controller === null) {
				$boxesByPosition[$box->position][] = $box;
				continue;
			}
			$positions = call_user_func([$controller, 'getSupportedPositions']);

			if (in_array('contentTop', $positions) || in_array('contentBottom', $positions)) {
				$boxesByPosition['content'][] = $box;
			}

			if (in_array('sidebarLeft', $positions)) {
				$boxesByPosition['sidebarLeft'][] = $box;
			}

			if (in_array('sidebarRight', $positions)) {
				$boxesByPosition['sidebarRight'][] = $box;
			}
		}

		$boxesByPosition['body'] = $boxesByPosition['content'];
		$boxesByPosition['sidebar'] = $boxesByPosition['sidebarRight'];

		WCF::getTPL()->assign([
			'boxList' => $boxList,
			'boxesByPosition' => $boxesByPosition
		]);

		return parent::getFormTemplate();
	}

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$box = new Box($content->boxID);

		if ($box === null) {
			return '';
		}

		$output = $box->render();
		$output = preg_replace_callback('/^<([A-Za-z]+)(.*)(class=\"([^"]+)\")/', function($match) use ($content) {
			return '<' . $match[1] . ' id="cmsContent' . $content->contentID . '"' . $match[2] . 'class="' . ((!empty($content->getCSSClasses()) ? $content->getCSSClasses() . ' ' : '')) . $match[4] . '"';
		}, $output);
		return $output;
	}

	/**
	 * @see \cms\system\content\type\IContentType::getSortableOutput()
	 */
	public function getSortableOutput(Content $content) {
		return '';
	}
}
