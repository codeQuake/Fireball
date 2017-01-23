-- since 2.2.0 Beta 1

ALTER TABLE cms1_content ADD COLUMN showHeadline TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE cms1_file ADD COLUMN width INT(10) NOT NULL DEFAULT 0;
ALTER TABLE cms1_file ADD COLUMN height INT(10) NOT NULL DEFAULT 0;
ALTER TABLE cms1_file ADD COLUMN filesizeThumbnail INT(10) NOT NULL DEFAULT 0;
ALTER TABLE cms1_file ADD COLUMN fileTypeThumbnail VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE cms1_file ADD COLUMN widthThumbnail INT(10) NOT NULL DEFAULT 0;
ALTER TABLE cms1_file ADD COLUMN heightThumbnail INT(10) NOT NULL DEFAULT 0;
ALTER TABLE cms1_file ADD COLUMN filename VARCHAR(255) NOT NULL DEFAULT '';
UPDATE cms1_file SET filename = title;

-- dashboard boxes
DROP TABLE IF EXISTS cms1_content_to_dashboardbox;
CREATE TABLE cms1_content_to_dashboardbox (
	contentID INT(10),
	boxID INT(10),
	position VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (contentID, boxID)
);

ALTER TABLE cms1_content_to_dashboardbox ADD FOREIGN KEY (contentID) REFERENCES cms1_content (contentID) ON DELETE CASCADE;
#ALTER TABLE cms1_content_to_dashboardbox ADD FOREIGN KEY (boxID) REFERENCES wcf1_dashboard_box (boxID) ON DELETE CASCADE;

ALTER TABLE cms1_page DROP FOREIGN KEY menuItemID;
