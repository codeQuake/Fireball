--links
DROP TABLE IF EXISTS cms1_content;
CREATE TABLE cms1_content (
contentID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
subject	VARCHAR(255) NOT NULL,
message	TEXT,
userID	INT(10),
username	VARCHAR(255),
time INT(10) NOT NULL,
languageID INT(10),
isActive	TINYINT(1) NOT NULL DEFAULT 0,
isDeleted	TINYINT(1) NOT NULL DEFAULT 0,
deleteTime INT(10) NULL,
lastChangeTime	INT(10),
attachments SMALLINT(5) NOT NULL DEFAULT 0,
enableSmilies TINYINT(1) NOT NULL DEFAULT 1,
enableHtml TINYINT(1) NOT NULL DEFAULT 0,
enableBBCodes	TINYINT(1) NOT NULL DEFAULT 1,
cumulativeLikes MEDIUMINT(7) NOT NULL DEFAULT 0
);

--foreign keys
ALTER TABLE cms1_content ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;
ALTER TABLE cms1_content ADD FOREIGN KEY (languageID) REFERENCES wcf1_language (languageID) ON DELETE SET NULL;