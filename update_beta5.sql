ALTER TABLE cms1_page ADD COLUMN isDisabled TINYINT(1) DEFAULT 0 AFTER menuItemID;

ALTER TABLE cms1_content ADD COLUMN isDisabled TINYINT(1) DEFAULT 0 AFTER showOrder;


--beta 7 update
ALTER TABLE cms1_page ADD COLUMN styleID INT(10) DEFAULT NULL AFTER isCommentable;
ALTER TABLE cms1_page ADD FOREIGN KEY (styleID) REFERENCES wcf1_style (styleID) ON DELETE SET NULL;
DROP TABLE IF EXISTS cms1_layout;
