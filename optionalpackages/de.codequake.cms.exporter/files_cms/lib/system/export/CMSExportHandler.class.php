<?php
namespace cms\system\export;

use cms\data\content\ContentList;
use cms\data\file\FileList;
use cms\data\folder\FolderList;
use cms\data\layout\LayoutList;
use cms\data\module\ModuleList;
use cms\data\news\NewsList;
use cms\data\page\PageList;
use cms\data\stylesheet\StylesheetList;
use wcf\data\language\item\LanguageItemList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\io\TarWriter;
use wcf\system\language\LanguageFactory;
use wcf\system\SingletonFactory;
use wcf\util\DirectoryUtil;
use wcf\util\StringUtil;
use wcf\util\XMLWriter;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class CMSExportHandler extends SingletonFactory {
	public $data = array();
	public $filename;

	public function init() {
		$this->getData();
	}

	public function getExportArchive() {
		$this->tar();
		return $this->filename;
	}

	protected function tar() {
		$this->filename = CMS_DIR . 'export/CMS-Export.' . StringUtil::getRandomID() . '.gz';
		
		$this->buildXML();
		$this->buildLangFiles();
		
		// files.tar
		$tar = new TarWriter(CMS_DIR . 'export/files.tar');
		$tar->add($this->getFiles(), '', CMS_DIR . 'files/');
		$tar->create();
		
		// templates.tar
		$tar = new TarWriter(CMS_DIR . 'export/templates.tar');
		$tar->add($this->getTemplates(), '', CMS_DIR . 'templates/');
		$tar->create();
		
		// tar
		$tar = new TarWriter($this->filename, true);
		
		$tar->add(CMS_DIR . 'export/cmsData.xml', '', CMS_DIR . 'export/');
		$tar->add(CMS_DIR . 'export/de.xml', '', CMS_DIR . 'export/');
		$tar->add(CMS_DIR . 'export/en.xml', '', CMS_DIR . 'export/');
		$tar->add(CMS_DIR . 'export/templates.tar', '', CMS_DIR . 'export/');
		$tar->add(CMS_DIR . 'export/files.tar', '', CMS_DIR . 'export/');
		$tar->create();
		@unlink(CMS_DIR . 'export/de.xml');
		@unlink(CMS_DIR . 'export/en.xml');
		@unlink(CMS_DIR . 'export/cmsData.xml');
		@unlink(CMS_DIR . 'export/files.tar');
		@unlink(CMS_DIR . 'export/templates.tar');
		@unlink(CMS_DIR . 'export/images.tar');
	}

	protected function getI18n() {
		$list = new LanguageItemList();
		$list->getConditionBuilder()->add('languageItemOriginIsSystem  = ?', array(
			0
		));
		$list->getConditionBuilder()->add('packageID = ?', array(
			PACKAGE_ID
		));
		$list->sqlOrderBy = 'languageCategoryID ASC';
		$list->readObjects();
		return $list->getObjects();
	}

	protected function buildLangFiles() {
		
		// german file
		$xml = new XMLWriter();
		$xml->beginDocument('language', 'http://www.woltlab.com', 'http://www.woltlab.com/XSD/maelstrom/language.xsd', array(
			'languageCode' => 'de'
		));
		$current = 0;
		foreach ($this->getI18n() as $item) {
			if (isset($item->languageID) && $item->languageID != 0 && LanguageFactory::getInstance()->getLanguageByCode('de') != null) {
				if ($item->languageID == LanguageFactory::getInstance()->getLanguageByCode('de')->languageID) {
					if ($current == 0 || $current != $item->languageCategoryID) {
						if ($current != 0) $xml->endElement();
						$xml->startElement('category', array(
							'name' => LanguageFactory::getInstance()->getCategoryByID($item->languageCategoryID)->languageCategory
						));
						$current = $item->languageCategoryID;
					}
					$xml->writeElement('item', $item->languageItemValue, array(
						'name' => $item->languageItem
					));
				}
			}
		}
		$xml->endDocument(CMS_DIR . 'export/de.xml');
		
		// english file
		$xml = new XMLWriter();
		$xml->beginDocument('language', 'http://www.woltlab.com', 'http://www.woltlab.com/XSD/maelstrom/language.xsd', array(
			'languageCode' => 'en'
		));
		$current = 0;
		foreach ($this->getI18n() as $item) {
			if (isset($item->languageID) && $item->languageID != 0 && LanguageFactory::getInstance()->getLanguageByCode('en') != null) {
				if ($item->languageID == LanguageFactory::getInstance()->getLanguageByCode('en')->languageID) {
					if ($current == 0 || $current != $item->languageCategoryID) {
						if ($current != 0) $xml->endElement();
						$xml->startElement('category', array(
							'name' => LanguageFactory::getInstance()->getCategoryByID($item->languageCategoryID)->languageCategory
						));
						$current = $item->languageCategoryID;
					}
					$xml->writeElement('item', $item->languageItemValue, array(
						'name' => $item->languageItem
					));
				}
			}
		}
		$xml->endDocument(CMS_DIR . 'export/en.xml');
	}

	protected function getFiles() {
		$du = new DirectoryUtil(CMS_DIR . 'files/');
		return $du->getFiles();
	}

	protected function getTemplates() {
		$du = new DirectoryUtil(CMS_DIR . 'templates/');
		$files = array();
		foreach ($du->getFiles() as $file) {
			if (preg_match('/cms_/', $file)) $files[] = $file;
		}
		return $files;
	}

	protected function buildXML() {
		$xml = new XMLWriter();
		$xml->beginDocument('data', '', '');
		
		// write page tags
		if (isset($this->data['pages'])) {
			foreach ($this->data['pages'] as $page) {
				$xml->startElement('page');
				$xml->writeElement('pageID', $page['pageID']);
				$xml->writeElement('alias', $page['alias']);
				$xml->writeElement('parentID', $page['parentID']);
				$xml->writeElement('title', $page['title']);
				if (isset($page['description'])) $xml->writeElement('description', $page['description']);
				if (isset($page['metaDescription'])) $xml->writeElement('metaDescription', $page['metaDescription']);
				if (isset($page['metaKeywords'])) $xml->writeElement('metaKeywords', $page['metaKeywords']);
				$xml->writeElement('invisible', $page['invisible']);
				$xml->writeElement('availableDuringOfflineMode', $page['availableDuringOfflineMode']);
				$xml->writeElement('robots', $page['robots']);
				$xml->writeElement('showOrder', $page['showOrder']);
				$xml->writeElement('isHome', $page['isHome']);
				$xml->writeElement('showSidebar', $page['showSidebar']);
				$xml->writeElement('sidebarOrientation', $page['sidebarOrientation']);
				$xml->writeElement('layoutID', $page['layoutID']);
				$xml->writeElement('isCommentable', $page['isCommentable']);
				$xml->writeElement('comments', $page['comments']);
				$xml->writeElement('clicks', $page['clicks']);
				$xml->endElement();
			}
		}
		
		// write content tags
		if (isset($this->data['contents'])) {
			foreach ($this->data['contents'] as $content) {
				$xml->startElement('content');
				$xml->writeElement('contentID', $content['contentID']);
				$xml->writeElement('pageID', $content['pageID']);
				$xml->writeElement('title', $content['title']);
				$xml->writeElement('showOrder', $content['showOrder']);
				if (isset($content['cssID'])) $xml->writeElement('cssID', $content['cssID']);
				if (isset($content['cssClasses'])) $xml->writeElement('cssClasses', $content['cssClasses']);
				$xml->writeElement('position', $content['position']);
				$xml->writeElement('type', $content['type']);
				$xml->startElement('sections');
				if (isset($content['sections'])) {
					foreach ($content['sections'] as $section) {
						$xml->startElement('section');
						$xml->writeElement('sectionID', $section['sectionID']);
						$xml->writeElement('contentID', $section['contentID']);
						$xml->writeElement('sectionType', $section['sectionType']);
						$xml->writeElement('sectionData', $section['sectionData']);
						$xml->writeElement('showOrder', $section['showOrder']);
						if (isset($section['cssID'])) $xml->writeElement('cssID', $section['cssID']);
						if (isset($section['cssClasses'])) $xml->writeElement('cssClasses', $section['cssClasses']);
						$xml->writeElement('additionalData', $section['additionalData']);
						$xml->endElement();
					}
				}
				$xml->endElement();
				$xml->endElement();
			}
		}
		
		// write folder tags
		if (isset($this->data['folders'])) {
			foreach ($this->data['folders'] as $folder) {
				$xml->startElement('folder');
				$xml->writeElement('folderID', $folder['folderID']);
				$xml->writeElement('folderName', $folder['folderName']);
				$xml->writeElement('folderPath', $folder['folderPath']);
				$xml->endElement();
			}
		}
		
		// write file tags
		if (isset($this->data['files'])) {
			foreach ($this->data['files'] as $file) {
				$xml->startElement('file');
				$xml->writeElement('fileID', $file['fileID']);
				$xml->writeElement('folderID', $file['folderID']);
				$xml->writeElement('title', $file['title']);
				$xml->writeElement('filename', $file['filename']);
				$xml->writeElement('size', $file['size']);
				$xml->writeElement('type', $file['type']);
				$xml->writeElement('downloads', $file['downloads']);
				$xml->endElement();
			}
		}
		
		// write layout tags
		if (isset($this->data['layouts'])) {
			foreach ($this->data['layouts'] as $layout) {
				$xml->startElement('layout');
				$xml->writeElement('layoutID', $layout['layoutID']);
				$xml->writeElement('title', $layout['title']);
				$xml->writeElement('data', $layout['data']);
				$xml->endElement();
			}
		}
		
		// write stylesheet tags
		if (isset($this->data['stylesheets'])) {
			foreach ($this->data['stylesheets'] as $sheet) {
				$xml->startElement('stylesheet');
				$xml->writeElement('sheetID', $sheet['sheetID']);
				$xml->writeElement('title', $sheet['title']);
				$xml->writeElement('less', $sheet['less']);
				$xml->endElement();
			}
		}
		
		// write module tags
		if (isset($this->data['modules'])) {
			foreach ($this->data['modules'] as $module) {
				$xml->startElement('module');
				$xml->writeElement('moduleID', $module['moduleID']);
				$xml->writeElement('moduleTitle', $module['moduleTitle']);
				if (isset($module['php'])) $xml->writeElement('php', $module['php']);
				if (isset($module['tpl'])) $xml->writeElement('tpl', $module['tpl']);
				$xml->endElement();
			}
		}
		
		$xml->endDocument(CMS_DIR . 'export/cmsData.xml');
	}

	protected function getData() {
		$this->loadPages();
		$this->loadContents();
		$this->loadFiles();
		$this->loadStyles();
		$this->loadModules();
	}

	protected function loadPages() {
		$list = new PageList();
		$list->readObjects();
		
		foreach ($list->getObjects() as $page) {
			$this->data['pages'][$page->pageID]['pageID'] = $page->pageID;
			$this->data['pages'][$page->pageID]['alias'] = $page->alias;
			$this->data['pages'][$page->pageID]['parentID'] = $page->parentID;
			$this->data['pages'][$page->pageID]['title'] = $page->title;
			$this->data['pages'][$page->pageID]['description'] = $page->description;
			$this->data['pages'][$page->pageID]['metaDescription'] = $page->metaDescription;
			$this->data['pages'][$page->pageID]['metaKeywords'] = $page->metaKeywords;
			$this->data['pages'][$page->pageID]['invisible'] = $page->invisible;
			$this->data['pages'][$page->pageID]['availableDuringOfflineMode'] = $page->availableDuringOfflineMode;
			$this->data['pages'][$page->pageID]['robots'] = $page->robots;
			$this->data['pages'][$page->pageID]['showOrder'] = $page->showOrder;
			$this->data['pages'][$page->pageID]['isHome'] = $page->isHome;
			$this->data['pages'][$page->pageID]['showSidebar'] = $page->showSidebar;
			$this->data['pages'][$page->pageID]['sidebarOrientation'] = $page->sidebarOrientation;
			$this->data['pages'][$page->pageID]['layoutID'] = $page->layoutID;
			$this->data['pages'][$page->pageID]['isCommentable'] = $page->isCommentable;
			$this->data['pages'][$page->pageID]['comments'] = $page->comments;
			$this->data['pages'][$page->pageID]['clicks'] = $page->clicks;
		}
	}

	protected function loadContents() {
		$list = new ContentList();
		$list->readObjects();
		foreach ($list->getObjects() as $content) {
			$this->data['contents'][$content->contentID]['contentID'] = $content->contentID;
			$this->data['contents'][$content->contentID]['pageID'] = $content->pageID;
			$this->data['contents'][$content->contentID]['title'] = $content->title;
			$this->data['contents'][$content->contentID]['showOrder'] = $content->showOrder;
			$this->data['contents'][$content->contentID]['cssClasses'] = $content->cssClasses;
			$this->data['contents'][$content->contentID]['position'] = $content->position;
			$this->data['contents'][$content->contentID]['type'] = $content->type;
			foreach ($content->getSections() as $section) {
				$this->data['contents'][$content->contentID]['sections'][$section->sectionID]['sectionID'] = $section->sectionID;
				$this->data['contents'][$content->contentID]['sections'][$section->sectionID]['contentID'] = $section->contentID;
				$this->data['contents'][$content->contentID]['sections'][$section->sectionID]['sectionType'] = ObjectTypeCache::getInstance()->getObjectType($section->sectionTypeID)->objectType;
				$this->data['contents'][$content->contentID]['sections'][$section->sectionID]['sectionData'] = $section->sectionData;
				$this->data['contents'][$content->contentID]['sections'][$section->sectionID]['showOrder'] = $section->showOrder;
				$this->data['contents'][$content->contentID]['sections'][$section->sectionID]['cssID'] = $section->cssID;
				$this->data['contents'][$content->contentID]['sections'][$section->sectionID]['cssClasses'] = $section->cssClasses;
				$this->data['contents'][$content->contentID]['sections'][$section->sectionID]['additionalData'] = $section->additionalData;
			}
		}
	}

	protected function loadFiles() {
		// load Folders
		$list = new FolderList();
		$list->readObjects();
		foreach ($list->getObjects() as $folder) {
			$this->data['folders'][$folder->folderID]['folderID'] = $folder->folderID;
			$this->data['folders'][$folder->folderID]['folderName'] = $folder->folderName;
			$this->data['folders'][$folder->folderID]['folderPath'] = $folder->folderPath;
		}
		
		// load files
		$list = new FileList();
		$list->readObjects();
		foreach ($list->getObjects() as $file) {
			$this->data['files'][$file->fileID]['fileID'] = $file->fileID;
			$this->data['files'][$file->fileID]['folderID'] = $file->folderID;
			$this->data['files'][$file->fileID]['title'] = $file->title;
			$this->data['files'][$file->fileID]['filename'] = $file->filename;
			$this->data['files'][$file->fileID]['size'] = $file->size;
			$this->data['files'][$file->fileID]['type'] = $file->type;
			$this->data['files'][$file->fileID]['downloads'] = $file->downloads;
		}
	}

	protected function loadStyles() {
		// load layouts
		$list = new LayoutList();
		$list->readObjects();
		foreach ($list->getObjects() as $layout) {
			$this->data['layouts'][$layout->layoutID]['layoutID'] = $layout->layoutID;
			$this->data['layouts'][$layout->layoutID]['title'] = $layout->title;
			$this->data['layouts'][$layout->layoutID]['data'] = $layout->data;
		}
		
		// load stylesheets
		$list = new StylesheetList();
		$list->readObjects();
		foreach ($list->getObjects() as $sheet) {
			$this->data['stylesheets'][$sheet->sheetID]['sheetID'] = $sheet->sheetID;
			$this->data['stylesheets'][$sheet->sheetID]['title'] = $sheet->title;
			$this->data['stylesheets'][$sheet->sheetID]['less'] = $sheet->less;
		}
	}

	protected function loadModules() {
		$list = new ModuleList();
		$list->readObjects();
		foreach ($list->getObjects() as $module) {
			$this->data['modules'][$module->moduleID]['moduleID'] = $module->moduleID;
			$this->data['modules'][$module->moduleID]['moduleTitle'] = $module->moduleTitle;
			$this->data['modules'][$module->moduleID]['php'] = $module->php;
			$this->data['modules'][$module->moduleID]['tpl'] = $module->tpl;
		}
	}
}
