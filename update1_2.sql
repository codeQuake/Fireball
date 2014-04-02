--update structre
ALTER TABLE cms1_page ADD availableDuringOfflineMode TINYINT(1) NOT NULL DEFAULT 0 AFTER invisible;
ALTER TABLE cms1_page ADD alias VARCHAR(255) NOT NULL DEFAULT '' AFTER pageID;