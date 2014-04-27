--update structre
ALTER TABLE cms1_page ADD availableDuringOfflineMode TINYINT(1) NOT NULL DEFAULT 0 AFTER invisible;
ALTER TABLE cms1_page ADD alias VARCHAR(255) NOT NULL DEFAULT '' AFTER pageID;

ALTER TABLE cms1_page ADD FOREIGN KEY (parentID) REFERENCES cms1_page (pageID) ON DELETE SET NULL;


ALTER TABLE cms1_news ADD showSignature TINYINT(1) NOT NULL DEFAULT 0 AFTER enableBBCodes;
