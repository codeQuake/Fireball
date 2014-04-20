<?php
namespace cms\acp\page;

use cms\data\page\PageNodeTree;
use wcf\page\AbstractPage;
use wcf\system\WCF;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class PageListPage extends AbstractPage {
    public $activeMenuItem = 'cms.acp.menu.link.cms.page.list';
    public $neededPermissions = array(
        'admin.cms.page.canListPage'
    );
    public $templateName = 'pageList';
    public $pageList = null;

    public function readData() {
        parent::readData();
        $this->pageList = new PageNodeTree(0);
    }

    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
            'pageList' => $this->pageList->getIterator()
        ));
    }
}
