ALTER TABLE cms1_page ADD alias VARCHAR(255) NOT NULL AFTER pageID;
ALTER TABLE cms1_page ADD availableDuringOfflineMode TINYINT(1) NOT NULL DEFAULT 0 AFTER invisible;