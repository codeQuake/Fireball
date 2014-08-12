ALTER TABLE cms1_page ADD COLUMN isDisabled TINYINT(1) DEFAULT 0 AFTER menuItemID;

ALTER TABLE cms1_content ADD COLUMN isDisabled TINYINT(1) DEFAULT 0 AFTER showOrder;


--beta 7 update

DROP TABLE IF EXISTS cms1_layout;
