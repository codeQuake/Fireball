<?php
use wcf\system\WCF;

$sql = "CREATE TABLE cms".WCF_N."_counter(
                                    time INT (20) NOT NULL,
                                    userID INT(10),
                                    browser VARCHAR(255),
                                    browserVersion VARCHAR(255),
                                    ipAddress VARCHAR(255),
                                    spider INT(20)
        );";
        
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array());

$sql = "ALTER TABLE cms".WCF."_page clicks INT (20) NOT NULL DEFAULT 0 AFTER comments";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array());