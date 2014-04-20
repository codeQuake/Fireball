<?php
namespace cms\data\news\image;

use wcf\data\AbstractDatabaseObjectAction;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class NewsImageAction extends AbstractDatabaseObjectAction {
    protected $className = 'cms\data\news\image\NewsImageEditor';
    protected $permissionsDelete = array(
        'admin.cms.news.canManageCategory'
    );
    protected $requireACP = array(
        'delete'
    );

    public function delete() {
        // del files
        foreach ($this->objectIDs as $objectID) {
            $file = new NewsImage($objectID);
            unlink(CMS_DIR . 'images/news/' . $file->filename);
        }
        parent::delete();
    }
}
