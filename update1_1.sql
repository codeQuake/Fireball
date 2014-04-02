CREATE TABLE cms1_counter(
            day INT(2) NOT NULL DEFAULT '1',
            month INT(2) NOT NULL DEFAULT '1',
            year INT(4) NOT NULL DEFAULT '2014',
            visits INT(20) NOT NULL DEFAULT 0,
            users INT(20) NOT NULL DEFAULT 0,
            spiders INT(20) NOT NULL DEFAULT 0,
            browsers VARCHAR(255)
            );
CREATE TABLE cms1_folder(
			folderID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			folderName VARCHAR(255) NOT NULL,
			folderPath VARCHAR(255) NOT NULL
            );

ALTER TABLE cms1_page ADD clicks INT (20) NOT NULL DEFAULT 0 AFTER comments;
ALTER TABLE cms1_file ADD folderID INT (10) NOT NULL DEFAULT 0 AFTER fileID;
ALTER TABLE cms1_news ADD imageID INT (10) NOT NULL DEFAULT 0 AFTER comments;
