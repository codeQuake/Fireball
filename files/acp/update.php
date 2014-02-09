<?php
use wcf\system\WCF;

$sql = "CREATE TABLE cms1_counter(visitorID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                    time INT (10) NOT NULL,
                                    userID INT(10),
                                    referrer VARCHAR(255),
                                    browser VARCHAR(255),
                                    browserVersion VARCHAR(255),
                                    resolution VARCHAR(255),
                                    ipAddress VARCHAR(255),
                                    pageID INT(10)
        );";
        
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array());