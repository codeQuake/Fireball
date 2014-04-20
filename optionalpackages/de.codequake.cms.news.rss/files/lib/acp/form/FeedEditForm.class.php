<?php
namespace cms\acp\form;

use cms\data\feed\Feed;
use cms\data\feed\FeedAction;
use cms\data\news\image\NewsImage;
use wcf\form\AbstractForm;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms.news.rss
 */
class FeedEditForm extends FeedAddForm {
    public $feedID = 0;
    public $feed = null;

    public function readData() {
        parent::readData();
        if (isset($_REQUEST['id'])) $this->feedID = intval($_REQUEST['id']);
        $this->feed = new Feed($this->feedID);
        $this->title = $this->feed->title;
        $this->feedUrl = $this->feed->feedUrl;
        $this->image = new NewsImage($this->feed->imageID);
    }

    public function readFormParameters() {
        parent::readFormParameters();
        if (isset($_REQUEST['id'])) $this->feedID = intval($_REQUEST['id']);
    }

    public function assignVariables() {
        parent::assignVariables();
        WCF::getTPL()->assign(array(
            'action' => 'edit',
            'feedID' => $this->feedID
        ));
    }

    public function save() {
        AbstractForm::save();
        $objectAction = new FeedAction(array(
            $this->feedID
        ), 'update', array(
            'title' => $this->title,
            'feedUrl' => $this->feedUrl,
            'lastCheck' => TIME_NOW,
            'categoryID' => $this->categoryID,
            'languageID' => $this->languageID,
            'imageID' => $this->image->imageID
        ));
        $objectAction->executeAction();
        
        $this->saved();
        WCF::getTPL()->assign('success', true);
    }
}
