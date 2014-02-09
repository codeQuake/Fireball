<?php
use wcf\system\WCF;

$sql = "CREATE TABLE cms1_counter(
                                    time INT (10) NOT NULL,
                                    userID INT(10),
                                    browser VARCHAR(255),
                                    browserVersion VARCHAR(255),
                                    ipAddress VARCHAR(255),
                                    spider TINYINT(1)
        );";
        
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array());