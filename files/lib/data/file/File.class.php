<?php
namespace cms\data\file;

use cms\data\folder\Folder;
use cms\data\CMSDatabaseObject;
use wcf\system\request\IRouteController;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class File extends CMSDatabaseObject implements IRouteController {
    protected static $databaseTableName = 'file';
    protected static $databaseTableIndexName = 'fileID';

    public function __construct($id, $row = null, $object = null) {
        if ($id !== null) {
            $sql = "SELECT *
                    FROM " . static::getDatabaseTableName() . "
                    WHERE (" . static::getDatabaseTableIndexName() . " = ?)";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute(array(
                $id
            ));
            $row = $statement->fetchArray();
            
            if ($row === false) $row = array();
        }
        
        parent::__construct(null, $row, $object);
    }

    public function getTitle() {
        return $this->title;
    }

    public function getPermission($permission = 'canDownloadFile') {
        return WCF::getSession()->getPermission('user.cms.content.' . $permission);
    }

    public function getIconTag() {
        if (preg_match('/image/i', $this->type)) return '<span class="icon icon16 icon-picture"></span>';
        if (preg_match('/audio/i', $this->type)) return '<span class="icon icon16 icon-music"></span>';
        if (preg_match('/video/i', $this->type)) return '<span class="icon icon16 icon-film"></span>';
        return '<span class="icon icon16 icon-file"></span>';
    }

    public function getFolder() {
        return new Folder($this->folderID);
    }
}
