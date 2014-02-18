<?php
use wcf\system\WCF;
//create counter table
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

//update page for click counting
$sql = "ALTER TABLE cms".WCF_N."_page ADD clicks INT (20) NOT NULL DEFAULT 0 AFTER comments";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array());

//create folder table
$sql ="CREATE TABLE cms1_folder(
                                folderID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                folderName VARCHAR(255) NOT NULL,
                                folderPath VARCHAR(255) NOT NULL
                                );";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array());

//update file table for folder support
$sql = "ALTER TABLE cms".WCF_N."_file ADD folderID INT (10) NOT NULL DEFAULT 0 AFTER fileID";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array());