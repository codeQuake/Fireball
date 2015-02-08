<?php
use wcf\system\WCF;

/**
 * @author	Jens Krumsieck
 * @copyright	2014-2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */

$sql = "SELECT * FROM wcf".WCF_N."_folder";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute();
while ($row = $statement->fetchArray()) {
    $dir = opendir(CMS_DIR.'images/'.$row['folderPath']);
    while(($file = readdir($dir)) !== false){
        if($file != '.' && $file != '..') {
            copy($dir.'/'.$file, CMS_DIR.'images/'.$file);
        }
    }
    closedir($dir);
    @unlink($dir);
}
?>