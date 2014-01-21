<?php
use wcf\system\WCF;

$sql = "ALTER TABLE cms".WCF_N."_content ADD type ENUM('div', 'ul', 'ol') NOT NULL DEFAULT 'div'";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();
