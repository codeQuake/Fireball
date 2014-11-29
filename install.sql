--page
DROP TABLE IF EXISTS cms1_page;
CREATE TABLE cms1_page (
	pageID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	alias VARCHAR(255) NOT NULL,
	parentID INT(10) DEFAULT NULL,
	title VARCHAR(255) NOT NULL DEFAULT '',
	description MEDIUMTEXT,
	metaDescription MEDIUMTEXT,
	metaKeywords VARCHAR(255),
	availableDuringOfflineMode TINYINT(1) DEFAULT 0,
	robots ENUM('index,follow', 'index,nofollow', 'noindex,follow', 'noindex,nofollow') NOT NULL DEFAULT 'index,follow',
	showOrder INT(10) DEFAULT 0,
	menuItemID INT(10),

	-- publication
	isDisabled TINYINT(1) DEFAULT 0,
	isPublished TINYINT(1) NOT NULL DEFAULT 1,
	publicationDate INT(10) NOT NULL DEFAULT 0,
	deactivationDate INT(10) NOT NULL DEFAULT 0,

	-- settings
	isHome INT(1) DEFAULT 0,
	showSidebar INT(1) DEFAULT 0,
	invisible TINYINT(1) DEFAULT 0,
	isCommentable TINYINT(1) NOT NULL DEFAULT 0,
	allowSubscribing TINYINT(1) NOT NULL DEFAULT 1,

	-- properties
	authorID INT(10) DEFAULT NULL,
	authorName VARCHAR(255) NOT NULL DEFAULT '',
	lastEditorID INT(10) DEFAULT NULL,
	lastEditorName VARCHAR(255) NOT NULL DEFAULT '',
	creationTime INT(10) NOT NULL DEFAULT 0,
	lastEditTime INT(10) NOT NULL DEFAULT 0,
	comments INT(10) NOT NULL DEFAULT 0,
	clicks INT (20) NOT NULL DEFAULT 0,

	-- display
	styleID INT(10) DEFAULT NULL,
	sidebarOrientation ENUM('left', 'right') NOT NULL DEFAULT 'right',
	stylesheets MEDIUMTEXT,
);

--page revisions
DROP TABLE IF EXISTS cms1_page_revision;
CREATE TABLE cms1_page_revision(
	revisionID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	pageID INT(10) NOT NULL,
	action VARCHAR(255),
	userID INT(10),
	username VARCHAR(255) NOT NULL DEFAULT '',
	time INT(10) NOT NULL DEFAULT 0,
	data MEDIUMTEXT
);

--content
DROP TABLE IF EXISTS cms1_content;
CREATE TABLE cms1_content (
	contentID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	parentID INT(10),
	pageID INT(10),
	title VARCHAR(255) NOT NULL DEFAULT '',
	contentTypeID INT(10),
	contentData MEDIUMTEXT,
	showOrder INT(10) DEFAULT 0,
	isDisabled TINYINT(1) DEFAULT 0,
	position ENUM('body', 'sidebar') NOT NULL DEFAULT 'body',
	cssID VARCHAR (255),
	cssClasses VARCHAR(255),
	additionalData MEDIUMTEXT DEFAULT NULL
);

--content revisions
DROP TABLE IF EXISTS cms1_content_revision;
CREATE TABLE cms1_content_revision(
	revisionID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	contentID INT(10) NOT NULL,
	action VARCHAR(255),
	userID INT(10),
	username VARCHAR(255) NOT NULL DEFAULT '',
	time INT(10) NOT NULL DEFAULT 0,
	data MEDIUMTEXT
);

--stylesheet
DROP TABLE IF EXISTS cms1_stylesheet;
CREATE TABLE cms1_stylesheet (
	sheetID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(255) NOT NULL,
	less MEDIUMTEXT
);

--file
DROP TABLE IF EXISTS cms1_file;
CREATE TABLE cms1_file (
	fileID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	categoryID INT(10) DEFAULT NULL,
	title VARCHAR(255) NOT NULL,
	size INT(10) NOT NULL,
	type VARCHAR(255) NOT NULL,
	downloads INT(10) DEFAULT 0
);

--counter
DROP TABLE IF EXISTS cms1_counter;
CREATE TABLE cms1_counter (
	day INT(2) NOT NULL DEFAULT '1',
	month INT(2) NOT NULL DEFAULT '1',
	year INT(4) NOT NULL DEFAULT '2014',
	visits INT(20) NOT NULL DEFAULT 0,
	users INT(20) NOT NULL DEFAULT 0,
	spiders INT(20) NOT NULL DEFAULT 0,
	browsers MEDIUMTEXT,
	platforms MEDIUMTEXT,
	devices MEDIUMTEXT
);

--foreign keys
ALTER TABLE cms1_content ADD FOREIGN KEY (pageID) REFERENCES cms1_page (pageID) ON DELETE CASCADE;

ALTER TABLE cms1_content ADD FOREIGN KEY (parentID) REFERENCES cms1_content (contentID) ON DELETE SET NULL;
ALTER TABLE cms1_content ADD FOREIGN KEY (contentTypeID) REFERENCES wcf1_object_type (objectTypeID) ON DELETE CASCADE;

ALTER TABLE cms1_content_revision ADD FOREIGN KEY (contentID) REFERENCES cms1_content (contentID) ON DELETE CASCADE;
ALTER TABLE cms1_content_revision ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;

ALTER TABLE cms1_file ADD FOREIGN KEY (categoryID) REFERENCES wcf1_category (categoryID) ON DELETE SET NULL;

ALTER TABLE cms1_page ADD FOREIGN KEY (parentID) REFERENCES cms1_page (pageID) ON DELETE SET NULL;
ALTER TABLE cms1_page ADD FOREIGN KEY (menuItemID) REFERENCES wcf1_page_menu_item (menuItemID) ON DELETE SET NULL;
ALTER TABLE cms1_page ADD FOREIGN KEY (styleID) REFERENCES wcf1_style (styleID) ON DELETE SET NULL;
ALTER TABLE cms1_page ADD FOREIGN KEY (authorID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE cms1_page ADD FOREIGN KEY (lastEditorID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;

ALTER TABLE cms1_page_revision ADD FOREIGN KEY (pageID) REFERENCES cms1_page (pageID) ON DELETE CASCADE;
ALTER TABLE cms1_page_revision ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
