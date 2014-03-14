<?php
use wcf\system\WCF;
//create counter table
$sql = "CREATE TABLE cms".WCF_N."_counter(
            day INT(2) NOT NULL DEFAULT '1',
            month INT(2) NOT NULL DEFAULT '1',
            year INT(4) NOT NULL DEFAULT '2014',
            visits INT(20) NOT NULL DEFAULT 0,
            users INT(20) NOT NULL DEFAULT 0,
            spiders INT(20) NOT NULL DEFAULT 0,
            browsers VARCHAR(255)
            );";
        
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array());

//update page for click counting
$sql = "ALTER TABLE cms".WCF_N."_page ADD clicks INT (20) NOT NULL DEFAULT 0 AFTER comments";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array());

//create folder table
$sql ="CREATE TABLE cms".WCF_N."_folder(
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

//news images
$sql = "CREATE TABLE cms".WCF_N."_news_image(
                                    imageID INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                    title VARCHAR(255) NOT NULL,
                                    filename VARCHAR(255) NOT NULL
                                    )";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array());

//update news table for news image support
$sql = "ALTER TABLE cms".WCF_N."_news ADD imageID INT (10) NOT NULL DEFAULT 0 AFTER comments";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute(array());