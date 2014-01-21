/** Update RC1 -> RC 2 **/
ALTER TABLE cms1_content ADD type ENUM('div', 'ul', 'ol') NOT NULL DEFAULT 'div'