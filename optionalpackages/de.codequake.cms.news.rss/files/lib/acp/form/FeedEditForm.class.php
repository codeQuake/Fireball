<?php
namespace cms\acp\form;
use wcf\form\AbstractForm;
use wcf\util\StringUtil;
use wcf\system\WCF;
use cms\data\feed\Feed;
use cms\data\feed\FeedAction;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms.news.rss
 */

class FeedEditForm extends FeedAddForm{
    
    public $feedID = 0;
    public $feed = null;
    
    public function readData(){
        parent::readData();
        if(isset($_REQUEST['id'])) $this->feedID = intval($_REQUEST['id']);
        $this->feed = new Feed($this->feedID);
        $this->title = $this->feed->title;
        $this->feedUrl = $this->feed->feedUrl;
    }
    
    public function readFormParameters(){
        parent::readFormParameters();
        if(isset($_REQUEST['id'])) $this->feedID = intval($_REQUEST['id']);
    }
    
    public function assignVariables(){
        parent::assignVariables();
        WCF::getTPL()->assign(array('action' => 'edit', 'feedID' => $this->feedID));
    }
    
    public function save(){
        AbstractForm::save();
        $objectAction = new FeedAction(array($this->feedID), 'update', array('data' => array('title' => $this->title, 'feedUrl' => $this->feedUrl)));
        $objectAction->executeAction();
        
        $this->saved();
        WCF::getTPL()->assign('success', true);
    }
}