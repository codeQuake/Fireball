<?php
namespace cms\acp\form;

use cms\data\news\image\NewsImageAction;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * Shows the news image add form.
 * 
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class NewsImageAddForm extends AbstractForm {
	public $neededModules = array(
		'MODULE_NEWS'
	);
	public $neededPermissions = array(
		'admin.cms.news.canManageCategory'
	);
	public $activeMenuItem = 'cms.acp.menu.link.cms.news.image.add';
	public $action = 'add';
	public $title = '';
	public $filename = '';

	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_FILES['image'])) $this->image = $_FILES['image'];
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
	}

	public function validate() {
		parent::validate();
		// check if file is given
		if (empty($this->image)) {
			throw new UserInputException('image', 'empty');
		}
		if (empty($this->image['tmp_name'])) throw new UserInputException('image', 'empty');
		
		$allowedTypes = ArrayUtil::trim(explode(",", 'jpg,png,JPEG,JPG,jpeg,gif,GIF,PNG'));
		$tmp = explode('.', $this->image['name']);
		$fileType = array_pop($tmp);
		if (! in_array($fileType, $allowedTypes)) throw new UserInputException('news', 'invalid');
	}

	public function save() {
		parent::save();
		$tmp = explode('.', $this->image['name']);
		$this->filename = 'FB-File-' . md5($this->image['tmp_name'] . time()) . '.' . array_pop($tmp);
		$path = CMS_DIR . 'images/news/' . $this->filename;
		move_uploaded_file($this->image['tmp_name'], $path);
		
		$data = array(
			'data' => array(
				'title' => $this->title,
				'filename' => $this->filename
			)
		);
		
		$action = new NewsImageAction(array(), 'create', $data);
		$action->executeAction();
		
		$this->saved();
		
		WCF::getTPL()->assign('success', true);
		
		$this->title = '';
		$this->filename = '';
	}

	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign(array(
			'title' => $this->title,
			'action' => $this->action
		));
	}
}
