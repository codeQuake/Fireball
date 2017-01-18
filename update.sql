ALTER TABLE cms1_stylesheet ADD scss MEDIUMTEXT;

ALTER TABLE cms1_content_to_dashboardbox DROP FOREIGN KEY contentID;
DROP TABLE cms1_content_to_dashboardbox;
