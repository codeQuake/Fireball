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