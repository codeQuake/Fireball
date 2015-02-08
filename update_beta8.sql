ALTER TABLE cms1_page CHANGE title title VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE cms1_page DROP robots;
ALTER TABLE cms1_page ADD allowIndexing TINYINT(1) NOT NULL DEFAULT 1 AFTER metaKeywords;
ALTER TABLE cms1_page DROP showSidebar;
ALTER TABLE cms1_page ADD allowSubscribing TINYINT(1) NOT NULL DEFAULT 1 AFTER isCommentable;
ALTER TABLE cms1_page DROP stylesheets;

ALTER TABLE cms1_stylesheet CHANGE sheetID stylesheetID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY;
ALTER TABLE cms1_file DROP folderID;

DROP TABLE IF EXISTS cms1_folder;

DROP TABLE IF EXISTS cms1_stylesheet_to_page;
CREATE TABLE cms1_stylesheet_to_page (
	stylesheetID INT(10) NOT NULL,
	pageID INT(10) NOT NULL,

	PRIMARY KEY (stylesheetID, pageID)
);

DROP TABLE IF EXISTS cms1_file_to_category;
CREATE TABLE cms1_file_to_category (
	fileID INT(10) NOT NULL,
	categoryID INT(10) NOT NULL,

	PRIMARY KEY (fileID, categoryID)
);
