<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\poll\PollManager;
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PollContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-bar-chart';

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		WCF::getTPL()->assign('poll', $content->getPoll());

		return WCF::getTPL()->fetch('poll', 'wcf');
	}

	/**
	 * @see	\cms\system\content\type\IContentType::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		PollManager::getInstance()->setObject('de.codequake.cms.content', 0);
	}

	/**
	 * @see	\cms\system\content\type\IContentType::readFormParameters()
	 */
	public function readFormParameters() {
		PollManager::getInstance()->readFormParameters();
	}

	/**
	 * @see	\cms\system\content\type\IContentType::validate()
	 */
	public function validate($data) {
		PollManager::getInstance()->validate();
	}
}
