<?php
namespace cms\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;
use wcf\util\HTTPRequest;
use wcf\util\StringUtil;
use cms\data\feed\FeedList;
use cms\data\feed\FeedEditor;
use cms\data\news\NewsAction;

class RSSFeedNewsCronjob extends AbstractCronjob{
    
        public function execute(Cronjob $cronjob){
            parent::execute($cronjob);
            $list = new FeedList();
            $list->readObjects();
            $news = array();
            foreach($list->getObjects() as $feed){
                try{
                    $request = new HTTPRequest($feed->feedUrl);
		            $request->execute();
		            $feedData = $request->getReply()['body'];
                }
                catch (\wcf\system\exception\SystemException $e){
			        //invalid URL
			        return ( array(	'errorMessage' => $e->getMessage()));
		        }
                
                if($xml = simplexml_load_string($feedData)){
                    @unlink($feedData);
                    $feedType = $this->getFeedType($xml);
                    switch($feedType){
                        case 'rss': 
                            foreach($xml->channel[0]->item as $item) {
                                if(strtotime((string) $item->pubDate) >= $feed->lastCheck){
                                    $content = '';
						            $ns_content = $item->children('http://purl.org/rss/1.0/modules/content/');
						            if(isset($ns_content) && (isset($ns_content->encoded)))$content = (string)$ns_content->encoded;
                                    
                                    if(empty($content)) $content = (string)$item->description;
                                    $content .= "<br/><span class='icon icon16 icon-rss'></span> [url='".(string) $item->guid."']".(string) $item->title."[/url] (".$feed->title.")";
                                    
                                    $news = array(
                                        'userID'       => null,
                                        'username'     =>  $feed->title,
                                        'subject'        => StringUtil::truncate((string) $item->title,254),
                                        'message'   => $content,
                                        'time'         => strtotime((string) $item->pubDate),
                                        'isDisabled' => (strtotime((string) $item->pubDate) > TIME_NOW)? 1 : 0,
                                        'enableBBCodes' => 1,
			                            'enableHtml' => 1,
			                            'enableSmilies' => 1,
                                        'imageID' => $feed->imageID,
                                        'lastChangeTime' => TIME_NOW);
                                    $categoryIDs = array($feed->categoryID);
                                    $action  = new NewsAction(array(), 'create', array('data' =>$news, 'categoryIDs' => $categoryIDs, 'attachmentHandler' => null, 'tags' => array()));
                                    $action->executeAction();
                                }
                            }
                      break;
                      case 'atom':
                            foreach($xml->children()->entry as $item){
                                if(strtotime((string) $item->children()->updated) >= $feed->lastCheck){
                                    $content = '';
                                    $content = (string)$item->children()->content;
                                    if(empty($content)) $content = (string)$item->summary;
                                    $url = $item->link->attributes();
                                    $content .= "<br/><span class='icon icon16 icon-rss'></span> [url='".(string) $url['href']."']".(string)$item->children()->title."[/url] (".$feed->title.")";
                                    $news = array(
                                        'userID' => null,
                                        'username' => $feed->title,
                                        'subject' => StringUtil::truncate((string)$item->children()->title, 254),
                                        'message' => $content,
                                        'time' => strtotime((string) $item->children()->updated),
                                        'isDisabled' => 0,
                                        'enableBBCodes' => 1,
			                            'enableHtml' => 1,
			                            'enableSmilies' => 1,
                                        'imageID' => $feed->imageID,
                                        'lastChangeTime' => TIME_NOW);
                                    
                                    $categoryIDs = array($feed->categoryID);
                                    $action  = new NewsAction(array(), 'create', array('data' =>$news, 'categoryIDs' => $categoryIDs, 'attachmentHandler' => null, 'tags' => array()));
                                    $action->executeAction();
                                }
                            }
                      break;
                      
                      case 'rdf':
                        foreach ($xml->item as $item) {
                            if(strtotime($item->children()->date) >= $feed->lastCheck){
                                $news = array(
                                    'userID' => null,
                                    'username' => $feed->title,
                                    'subject' => StringUtil::truncate((string)$item->title, 254),
                                    'message' => (string)$item->description."<br/><span class='icon icon16 icon-rss'></span> [url='".(string)$item->link."']".(string)$item->title."[/url] (".$feed->title.")",
                                    'time' => strtotime($item->children()->date),
                                    'isDisabled' => 0,
                                    'enableBBCodes' => 1,
                                    'enableHtml' => 1,
                                    'enableSmilies' => 1,
                                    'imageID' => $feed->imageID,
                                    'lastChangeTime' => TIME_NOW);
                                    
                                    $categoryIDs = array($feed->categoryID);
                                    $action  = new NewsAction(array(), 'create', array('data' =>$news, 'categoryIDs' => $categoryIDs, 'attachmentHandler' => null, 'tags' => array()));
                                    $action->executeAction();
                                
                            }
                            
                        }
                      break;
                    }
                    
                }
                $feedEditor = new FeedEditor($feed);
                $feedEditor->update(array('lastCheck' => TIME_NOW));
            }
            
            
        }
        
        public function getFeedType($xml){
            // get feed type
            if (isset($xml->channel->item))	return 'rss';
            elseif (isset($xml->item))	return 'rdf';
            elseif (isset($xml->entry))	return 'atom';
            else return null;
        }
}   