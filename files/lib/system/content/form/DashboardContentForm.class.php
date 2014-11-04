<?php
namespace cms\system\content\form;
use cms\data\content\ContentAction;
use wcf\system\cache\builder\DashboardBoxCacheBuilder;
use wcf\system\exception\UserInputException;

/**
 * Handles inputs for a dashboard box content.
 * 
 * @author	Florian Frantzen
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class DashboardContentForm extends AbstractContentForm {
	/**
	 * id of the dashboard box
	 * @var	integer
	 */
	public $boxID = 0;

	/**
	 * @see	\cms\system\content\form\AbstractContentForm::$objectTypeName
	 */
	protected static $objectTypeName = 'de.codequake.cms.content.type.dashboard';

	/**
	 * @see	\cms\system\content\form\IContentForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['content_'.$this->identifier.'_boxID'])) $this->boxID = intval($_POST['content_'.$this->identifier.'_boxID']);
	}

	/**
	 * @see	\cms\system\content\form\IContentForm::validate()
	 */
	public function validate() {
		parent::validate();

		$boxes = DashboardBoxCacheBuilder::getInstance()->getData(array(), 'boxes');
		if (!isset($boxes[$this->boxID])) {
			throw new UserInputException('content_'.$this->identifier.'_boxID');
		}
		if (($this->position == 'body' && $boxes[$this->boxID]->boxType != 'content') || ($this->position == 'sidebar' && $boxes[$this->boxID]->boxType != 'sidebar')) {
			throw new UserInputException('content_'.$this->identifier.'_boxID');
		}
	}

	/**
	 * @see	\cms\system\content\form\AbstractContentForm::create()
	 */
	protected function create() {
		parent::create();

		$this->additionalData = array_merge($this->additionalData, array(
			'boxID' => $this->boxID
		));

		$data = array(
			'pageID' => $this->pageID,
			'contentTypeID' => $this->getObjectType()->objectTypeID,
			'showOrder' => $this->showOrder,
			'position' => $this->position,
			'contentData' => serialize($this->additionalData)
		);

		$this->objectAction = new ContentAction(array(), 'create', array('data' => $data));
		$this->objectAction->executeAction();

		$this->saved();
	}

	/**
	 * @see	\cms\system\content\form\AbstractContentForm::update()
	 */
	protected function update() {
		parent::update();

		$this->additionalData = array_merge($this->additionalData, array(
			'boxID' => $this->boxID
		));

		$data = array(
			'showOrder' => $this->showOrder,
			'contentData' => serialize($this->additionalData)
		);

		$this->objectAction = new ContentAction(array($this->identifier), 'update', array('data' => $data));
		$this->objectAction->executeAction();

		$this->saved();
	}

	/**
	 * @see	\cms\system\content\form\IContentForm::getFormVariables()
	 */
	public function getFormVariables() {
		return array_merge(parent::getFormVariables(), array(
			'boxID' => $this->boxID
		));
	}
}
