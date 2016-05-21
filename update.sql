ALTER TABLE cms1_page CHANGE isHome isHome TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE cms1_page CHANGE clicks clicks INT (10) NOT NULL DEFAULT 0;
ALTER TABLE cms1_page CHANGE showOrder showOrder INT(10) NOT NULL DEFAULT 0;
ALTER TABLE cms1_page CHANGE invisible invisible TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE cms1_page CHANGE availableDuringOfflineMode availableDuringOfflineMode TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE cms1_counter CHANGE visits visits INT(10) NOT NULL DEFAULT 0;
ALTER TABLE cms1_counter CHANGE users users INT(10) NOT NULL DEFAULT 0;
ALTER TABLE cms1_counter CHANGE spiders spiders INT(10) NOT NULL DEFAULT 0;
