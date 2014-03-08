<?php
namespace cms\acp\form;
use wcf\form\AbstractForm;
use wcf\util\StringUtil;
use wcf\system\WCF;
use wcf\system\exception\UserInputException;
use cms\data\feed\FeedAction;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms.news.rss
 */

class FeedAddForm extends AbstractForm{

    public $templateName = 'feedAdd';
    public $neededPermissions = array('admin.cms.news.canAddFeed');
    public $activeMenuItem = 'cms.acp.menu.link.cms.feed.add';
    
    public $title = '';
    public $feedUrl = '';
    
   
    public function readFormParameters(){
        parent::readFormParameters();
        
        if(isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
        if(isset($_POST['feedUrl'])) $this->feedUrl = StringUtil::trim($_POST['feedUrl']);
    }
    
    public function save(){
        parent::save();
        
        $objectAction = new FeedAction(array(), 'create', array('data' => array('title' => $this->title, 'feedUrl' => $this->feedUrl, 'lastCheck' => TIME_NOW)));
        $objectAction->executeAction();
       
        $this->saved();
        WCF::getTPL()->assign('success', true);
        
        $this->title = '';
        $this->feedUrl = '';
    }
    
    public function validate(){
        parent::validate();
        if(!simplexml_load_file($this->url)) throw new UserInputException('feedUrl', 'noFeed');
    }
    
    public function assignVariables(){
        parent::assignVariables();
        
        WCF::getTPL()->assign(array('title' => $this->title, 'feedUrl' => $this->feedUrl, 'action' => 'add'));
    }
}