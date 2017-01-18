ALTER TABLE cms1_stylesheet ADD scss INT(10) NOT NULL DEFAULT 0;

ALTER TABLE cms1_content_to_dashboardbox DROP FOREIGN KEY contentID;
DROP TABLE cms1_content_to_dashboardbox;
