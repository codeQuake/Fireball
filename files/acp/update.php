<?php

$sql = "CREATE TABLE cms1_counter(  
                                    time INT (10) NOT NULL,
                                    referrer VARCHAR(255),
                                    browser VARCHAR(255),
                                    browserVersion VARCHAR(255),
                                    resolution VARCHAR(255),
                                    ipAddress VARCHAR(255)
);";