ALTER TABLE cms1_page CHANGE title title VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE cms1_page DROP robots;
ALTER TABLE cms1_page ADD allowIndexing TINYINT(1) NOT NULL DEFAULT 1 AFTER metaKeywords;
ALTER TABLE cms1_page DROP showSidebar;
ALTER TABLE cms1_page ADD allowSubscribing TINYINT(1) NOT NULL DEFAULT 1 AFTER isCommentable;
