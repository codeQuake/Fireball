<?php
use wcf\system\WCF;

$sql = "CREATE TABLE cms1_counter(
                                    time INT (10) NOT NULL,
                                    userID INT(10),
                                    referrer VARCHAR(255),
                                    browser VARCHAR(255),
                                    browserVersion VARCHAR(255),
                                    resolution VARCHAR(255),
                                    ipAddress VARCHAR(255)
        );";
        
$statement = WCF::getDB()->prepareStatement($sql);
WCF::getDB()->execute($statement, array());