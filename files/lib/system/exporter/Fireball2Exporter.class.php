<?php

namespace cms\system\exporter;
use wcf\data\category\Category;
use wcf\data\category\CategoryEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\database\DatabaseException;
use wcf\system\exporter\AbstractExporter;
use wcf\system\importer\ImportHandler;
use wcf\system\WCF;
use wcf\util\FileUtil;

/**
 * Provides an exporter from Fireball CMS 2.0/2.1 into Fireball CMS 2.1
 *
 * @author	Florian Gail
 * @copyright	2013 - 2016 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class Fireball2Exporter extends AbstractExporter {
	/**
	 * wcf installation number
	 * @var	integer
	 */
	protected $dbNo = 0;
	
	/**
	 * array with cached categories from old installation
	 * @var array
	 */
	protected $categoryCache = array();
	
	/**
	 * @see \wcf\system\exporter\AbstractExporter::$methods
	 */
	protected $methods = array(
		'de.codequake.cms.page' => 'Pages',
		'de.codequake.cms.content' => 'Contents',
		'de.codequake.cms.file.category' => 'FileCategories',
		'de.codequake.cms.file' => 'Files',
		'de.codequake.cms.page.comment' => 'Comments',
		'de.codequake.cms.page.comment.response' => 'CommentResponses',
		'de.codequake.cms.stylesheet' => 'Stylesheets'
	);
	
	/**
	 * @see	\wcf\system\exporter\AbstractExporter::$limits
	 */
	protected $limits = array(
		'de.codequake.cms.page' => 300,
		'de.codequake.cms.content' => 100,
		'de.codequake.cms.file.category' => 300,
		'de.codequake.cms.file' => 100,
		'de.codequake.cms.page.comment' => 300,
		'de.codequake.cms.page.comment.response' => 300,
		'de.codequake.cms.stylesheet' => 300
	);
	
	/**
	 * @see	\wcf\system\exporter\IExporter::init()
	 */
	public function init() {
		parent::init();
		
		if (preg_match('/^cms(\d+)_$/', $this->databasePrefix, $match))
			$this->dbNo = $match[1];
	}
	
	/**
	 * @see	\wcf\system\exporter\IExporter::validateFileAccess()
	 */
	public function validateFileAccess() {
		if (in_array('de.codequake.cms.file', $this->selectedData)) {
			if (empty($this->fileSystemPath) || !@file_exists($this->fileSystemPath.'lib/core.functions.php')) {
				return false;
			} else {
				return true;
			}
		}
	}
	
	/**
	 * @see \wcf\system\exporter\IExporter::getSupportedData()
	 */
	public function getSupportedData() {
		return array(
			'de.codequake.cms.page' => array(
				'de.codequake.cms.page.comment',
				'de.codequake.cms.content'
			),
			'de.codequake.cms.file.category' => array(
				'de.codequake.cms.file'
			),
			'de.codequake.cms.stylesheet' => array()
		);
	}
	
	/**
	 * @see \wcf\system\exporter\IExporter::validateDatabaseAccess()
	 */
	public function validateDatabaseAccess() {
		parent::validateDatabaseAccess();
		
		$sql = "SELECT	packageID, packageDir, packageVersion
			FROM	wcf".$this->dbNo."_package
			WHERE	package = ?";
		$statement = $this->database->prepareStatement($sql, 1);
		$statement->execute(array('de.codequake.cms'));
		$row = $statement->fetchArray();
		
		if ($row !== false) {
			if (substr($row['packageVersion'], 0, 3) != '2.1' && substr($row['packageVersion'], 0, 3) != '2.0')
				throw new DatabaseException('Cannot find Fireball CMS 2.0/2.1 installation', $this->database);
		} else {
			throw new DatabaseException('Cannot find Fireball CMS installation', $this->database);
		}
	}
	
	/**
	 * @see \wcf\system\exporter\IExporter::getQueue()
	 */
	public function getQueue() {
		$queue = array();
		
		if (in_array('de.codequake.cms.stylesheet', $this->selectedData)) {
			$queue[] = 'de.codequake.cms.stylesheet';
		}
		
		if (in_array('de.codequake.cms.file.category', $this->selectedData)) {
			$queue[] = 'de.codequake.cms.file.category';
			
			if (in_array('de.codequake.cms.file', $this->selectedData))
				$queue[] = 'de.codequake.cms.file';
		}
		
		if (in_array('de.codequake.cms.page', $this->selectedData)) {
			$queue[] = 'de.codequake.cms.page';
			
			if (in_array('de.codequake.cms.content', $this->selectedData))
				$queue[] = 'de.codequake.cms.content';
			
			if (in_array('de.codequake.cms.page.comment', $this->selectedData)) {
				$queue[] = 'de.codequake.cms.page.comment';
				$queue[] = 'de.codequake.cms.page.comment.response';
			}
		}
		
		return $queue;
	}
	
	/**
	 * @see	\wcf\system\exporter\IExporter::getDefaultDatabasePrefix()
	 */
	public function getDefaultDatabasePrefix() {
		return 'cms1_';
	}
	
	/**
	 * Counts pages.
	 */
	public function countPages() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	cms" . $this->dbNo . "_page";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * Exports pages.
	 */
	public function exportPages($offset, $limit) {
		$sql = "SELECT	*
			FROM	cms" . $this->dbNo . "_page
			ORDER BY	pageID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			$row['objectTypeID'] = ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.codequake.cms.page.type', 'de.codequake.cms.page.type.page')->objectTypeID;
			ImportHandler::getInstance()->getImporter('de.codequake.cms.page')->import($row['pageID'], $row);
		}
	}
	
	/**
	 * Counts contents.
	 */
	public function countContents() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	cms" . $this->dbNo . "_content";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * Exports contents.
	 */
	public function exportContents($offset, $limit) {
		$sql = "SELECT	*
			FROM	cms" . $this->dbNo . "_content
			ORDER BY	contentID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.codequake.cms.content')->import($row['contentID'], $row);
		}
	}
	
	/**
	 * Counts stylesheets.
	 */
	public function countStylesheets() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	cms" . $this->dbNo . "_stylesheet";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * Exports stylesheets.
	 */
	public function exportStylesheets($offset, $limit) {
		$sql = "SELECT	*
			FROM	cms" . $this->dbNo . "_stylesheet
			ORDER BY	stylesheetID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.codequake.cms.stylesheet')->import($row['stylesheetID'], $row);
		}
	}
	
	/**
	 * Counts categories.
	 */
	public function countFileCategories() {
		$objectTypeID = $this->getObjectTypeID('com.woltlab.wcf.category', 'de.codequake.cms.file');
		
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".$this->dbNo."_category
			WHERE	objectTypeID = ?";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array($objectTypeID));
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * Exports categories.
	 */
	public function exportFileCategories($offset, $limit) {
		$objectTypeID = $this->getObjectTypeID('com.woltlab.wcf.category', 'de.codequake.cms.file');
		
		$sql = "SELECT	*
			FROM	wcf".$this->dbNo."_category
			WHERE	objectTypeID = ?
			ORDER BY parentCategoryID, showOrder, categoryID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute(array($objectTypeID));
		
	        while ($row = $statement->fetchArray()) {
	            $this->categoryCache[$row['parentCategoryID']][] = $row;
	        }
		
	        $this->exportCategoriesRecursively();
	}
	
	/**
	 * Exports the categories of the given parent recursively.
	 *
	 * @param int $parentID
	 */
	protected function exportCategoriesRecursively($parentID = 0) {
		if (!isset($this->categoryCache[$parentID])) {
			return;
		}
		
		foreach ($this->categoryCache[$parentID] as $category) {
			$additionalData = @unserialize($category['additionalData']);
			
			// import category
			$categoryID = ImportHandler::getInstance()->getImporter('de.codequake.cms.file.category')->import($category['categoryID'], array(
				'parentCategoryID' => $category['parentCategoryID'],
				'title' => $category['title'],
				'description' => $category['description'],
				'showOrder' => $category['showOrder'],
				'time' => $category['time'],
				'isDisabled' => $category['isDisabled'],
				'additionalData' => serialize(array()),
			));
			
			$this->updateCategoryI18nData($categoryID, $category);
			
			$this->exportCategoriesRecursively($category['categoryID']);
		}
	}
	
	/**
	 * Counts files.
	 */
	public function countFiles() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	cms" . $this->dbNo . "_file";
		$statement = $this->database->prepareStatement($sql);
		$statement->execute();
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * Exports files.
	 */
	public function exportFiles($offset, $limit) {
		$sql = "SELECT	packageDir
			FROM	wcf".$this->dbNo."_package
			WHERE	package = ?";
		$statement = $this->database->prepareStatement($sql, 1);
		$statement->execute(array('de.codequake.cms'));
		$packageDir = $statement->fetchColumn();
		$path = FileUtil::getRealPath($this->fileSystemPath . '/' . $packageDir);
		
		$sql = "SELECT	*
			FROM	cms" . $this->dbNo . "_file
			ORDER BY	fileID";
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute();
		
		while ($row = $statement->fetchArray()) {
			$additionalData = array();
			$additionalData['fileLocation'] = $path . 'files/' . substr($row['fileHash'], 0, 2) . '/' . $row['fileID'] . '-' . $row['fileHash'];
			
			ImportHandler::getInstance()->getImporter('de.codequake.cms.file')->import($row['fileID'], $row, $additionalData);
		}
	}
	
	/**
	 * Counts comments.
	 *
	 * @return int
	 */
	public function countComments() {
		$objectTypeID = $this->getObjectTypeID('com.woltlab.wcf.comment.commentableContent', 'de.codequake.cms.page.comment');
		
		$sql = 'SELECT COUNT(*) AS count
			FROM wcf'.$this->dbNo.'_comment
			WHERE objectTypeID = ?';
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array($objectTypeID));
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * Exports comments.
	 *
	 * @param int $offset
	 * @param int $limit
	 */
	public function exportComments($offset, $limit) {
		$objectTypeID = $this->getObjectTypeID('com.woltlab.wcf.comment.commentableContent', 'de.codequake.cms.page.comment');
		
		$sql = 'SELECT *
			FROM wcf'.$this->dbNo.'_comment
			WHERE objectTypeID = ?
			ORDER BY commentID';
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute(array($objectTypeID));
		
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.codequake.cms.page.comment')->import($row['commentID'], array(
				'objectID' => $row['objectID'],
				'userID' => $row['userID'],
				'username' => $row['username'],
				'message' => $row['message'],
				'time' => $row['time'],
				'objectTypeID' => $objectTypeID,
				'responses' => 0,
				'responseIDs' => serialize(array()),
			));
		}
	}
	
	/**
	 * Counts comment responses.
	 *
	 * @return int
	 */
	public function countCommentResponses() {
		$objectTypeID = $this->getObjectTypeID('com.woltlab.wcf.comment.commentableContent', 'de.codequake.cms.page.comment');
		
		$sql = 'SELECT COUNT(*) AS count
	            FROM wcf'.$this->dbNo.'_comment_response
	            WHERE commentID IN (
	                SELECT commentID
	                FROM wcf'.$this->dbNo.'_comment
	                WHERE	objectTypeID = ?
	            )';
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array($objectTypeID));
		$row = $statement->fetchArray();
		
		return $row['count'];
	}
	
	/**
	 * Exports comment responses.
	 *
	 * @param int $offset
	 * @param int $limit
	 */
	public function exportCommentResponses($offset, $limit) {
		$objectTypeID = $this->getObjectTypeID('com.woltlab.wcf.comment.commentableContent', 'de.codequake.cms.page.comment');
		
		$sql = 'SELECT *
	            FROM wcf'.$this->dbNo.'_comment_response
	            WHERE commentID IN (
	                SELECT commentID
	                FROM wcf'.$this->dbNo.'_comment
	                WHERE objectTypeID = ?
	            )
	            ORDER BY responseID';
		$statement = $this->database->prepareStatement($sql, $limit, $offset);
		$statement->execute(array($objectTypeID));
		
		while ($row = $statement->fetchArray()) {
			ImportHandler::getInstance()->getImporter('de.codequake.cms.page.comment.response')->import($row['responseID'], array(
				'commentID' => $row['commentID'],
				'time' => $row['time'],
				'userID' => $row['userID'],
				'username' => $row['username'],
				'message' => $row['message'],
			));
		}
	}
	
	/**
	 * Updates the i18n data of the category with the given id.
	 *
	 * @param int   $categoryID
	 * @param array $category
	 */
	private function updateCategoryI18nData($categoryID, array $category) {
		// get title
		if (preg_match('~wcf.category.category.title.category\d+~', $category['title'])) {
			$titleValues = $this->getLanguageItemValues($category['title']);
			$title = $this->importLanguageVariable('wcf.category', 'wcf.category.category.title.category'.$categoryID, $titleValues);
			if ($title === false) {
				$title = 'Imported Category '.$categoryID;
			}
		}
		
		// get description
		if (preg_match('~wcf.category.category.title.category\d+.description~', $category['description'])) {
			$descriptionValues = $this->getLanguageItemValues($category['description']);
			$description = $this->importLanguageVariable('wcf.category', 'wcf.category.category.description.category'.$categoryID, $descriptionValues);
			if ($description === false) {
				$description = '';
			}
		}
		
		// update category
		$updateData = array();
		if (!empty($title)) {
			$updateData['title'] = $title;
		}
		if (!empty($description)) {
			$updateData['description'] = $description;
		}
		
		if (count($updateData)) {
			$importedCategory = new Category(null, array('categoryID' => $categoryID));
			$editor = new CategoryEditor($importedCategory);
			$editor->update($updateData);
		}
	}
	
	/**
	 * Returns the values of the language item with the given name.
	 *
	 * @param string $languageItem
	 *
	 * @return array
	 */
	private function getLanguageItemValues($languageItem) {
		$sql = '
	            SELECT language_item.languageItemValue, language_item.languageCustomItemValue, language_item.languageUseCustomValue, language.languageCode
	            FROM wcf'.$this->dbNo.'_language_item language_item
	            LEFT JOIN wcf'.$this->dbNo.'_language language ON (language.languageID = language_item.languageID)
	            WHERE language_item.languageItem = ?';
		$statement = $this->database->prepareStatement($sql);
		$statement->execute(array($languageItem));
		
		$values = array();
		while ($row = $statement->fetchArray()) {
			$values[$row['languageCode']] = ($row['languageUseCustomValue'] ? $row['languageCustomItemValue'] : $row['languageItemValue']);
		}
		
		return $values;
	}
	
	/**
	 * Returns the id of the object type with the given name.
	 *
	 * @param	string		$definitionName
	 * @param	string		$objectTypeName
	 * @return	integer
	 */
	protected function getObjectTypeID($definitionName, $objectTypeName) {
		$sql = "SELECT	objectTypeID
			FROM	wcf".$this->dbNo."_object_type
			WHERE	objectType = ?
				AND definitionID = (
					SELECT definitionID FROM wcf".$this->dbNo."_object_type_definition WHERE definitionName = ?
				)";
		$statement = $this->database->prepareStatement($sql, 1);
		$statement->execute(array($objectTypeName, $definitionName));
		$row = $statement->fetchArray();
		
		if ($row !== false)
			return $row['objectTypeID'];
		return null;
	}
}
