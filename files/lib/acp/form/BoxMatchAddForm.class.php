<?php

namespace cms\acp\form;

use cms\data\content\Content;
use cms\data\content\DrainedContentNodeTree;
use cms\data\content\match\ContentBoxMatchAction;
use wcf\data\dashboard\box\DashboardBoxAction;
use wcf\data\language\item\LanguageItemEditor;
use wcf\data\package\PackageCache;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Shows the add form for matchings content <> box.
 * 
 * @author	Florian Gail
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class BoxMatchAddForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'fireball.acp.menu.link.fireball.boxmatch.add';

	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.fireball.content.canAddContent');

	/**
	 * id of the content object
	 * @var integer
	 */
	public $contentID = 0;

	/**
	 * position of the new box
	 * @var string
	 */
	public $position = '';

	/**
	 * content node tree
	 * @var DrainedContentNodeTree
	 */
	public $contentNodeTree = null;

	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['contentID'])) $this->contentID = intval($_POST['contentID']);
		if (isset($_POST['position'])) $this->position = intval($_POST['position']);
	}

	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();

		if (empty($this->contentID)) {
			throw new UserInputException('contentID');
		}
		$content = new Content($this->contentID);
		if ($content === null) {
			throw new UserInputException('contentID');
		}

		if (empty($this->position)) {
			throw new UserInputException('position');
		}
		//TODO: change for WSC 3.0
		if (!in_array($this->position, array('content', 'sidebar'))) {
			throw new UserInputException('position', 'inValid');
		}
	}

	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();

		$package = PackageCache::getInstance()->getPackageByIdentifier('de.codequake.cms');

		$this->objectAction = new DashboardBoxAction(array(), 'create', array(
			'data' => array(
				'boxName' => 'de.codequake.cms.content' . $this->contentID,
				'boxType' => $this->position,
				'className' => 'cms\\system\\dashboard\\box\\ContentDashboardBox',
				'packageID' => $package->packageID
			)
		));
		$returnValues = $this->objectAction->executeAction();
		$box = $returnValues['returnValues'];
		$content = new Content($this->contentID);

		$sql = "SELECT	languageCategoryID
			FROM	wcf".WCF_N."_language_category
			WHERE	languageCategory = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array('wcf.dashboard'));
		$row = $statement->fetchArray();
		$languageCategoryID = $row['languageCategoryID'];

		$availableLanguages = LanguageFactory::getInstance()->getLanguages();
		/** @var \wcf\data\language\Language $language */
		foreach ($availableLanguages as $language) {
			LanguageItemEditor::create(array(
				'languageID' => $language->languageID,
				'languageItem' => 'wcf.dashboard.box.de.codequake.cms.content' . $this->contentID,
				'languageItemValue' => $language->get($content->title) . ' (Fireball CMS - Content #' . $this->contentID . ')',
				'languageItemOriginIsSystem' => 0,
				'languageCategoryID' => $languageCategoryID,
				'packageID' => $package->packageID
			));
		}

		$matchAction = new ContentBoxMatchAction(array(), 'create', array(
			'data' => array(
				'contentID' => $this->contentID,
				'boxID' => $box->boxID,
				'position' => $this->position
			)
		));
		$matchAction->executeAction();

		$this->saved();

		// show success message
		WCF::getTPL()->assign('success', true);

		// reset variables
		$this->contentID = 0;
		$this->position = '';
	}

	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		$this->contentNodeTree = new DrainedContentNodeTree();
	}

	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'action' => 'add',
			'contentID' => $this->contentID,
			'position' => $this->position,
			'contentNodeTree' => $this->contentNodeTree
		));
	}
}
