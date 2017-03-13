ALTER TABLE cms1_stylesheet ADD scss MEDIUMTEXT;

ALTER TABLE cms1_content_to_dashboardbox DROP FOREIGN KEY contentID;
DROP TABLE cms1_content_to_dashboardbox;

ALTER TABLE cms1_page DROP FOREIGN KEY menuItemID;

-- 3.0.0 Beta 2
ALTER TABLE cms1_content CHANGE position position VARCHAR(255) NOT NULL;
ALTER TABLE cms1_page ADD COLUMN wcfPageID INT(10);

ALTER TABLE cms1_page ADD FOREIGN KEY (wcfPageID) REFERENCES wcf1_page (pageID) ON DELETE SET NULL;