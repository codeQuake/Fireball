<?php
namespace cms\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;

use cms\data\feed\FeedList;
use cms\data\news\NewsAction;

class RSSFeedNewsCronjob extends AbstractCronjob{
    
        public function execute(Cronjob $cronjob){
            parent::execute($cronjob);
            $list = new FeedList();
            $list->readObjects();
            $news = array();
            foreach($list->getObjects() as $feed){
                if($xml = simplexml_load_file($feed->feedUrl)){
                    foreach($xml->channel[0]->item as $item) {
                        if(strtotime((string) $item->pubDate) >= $feed->lastCheck){
                            $content = '';
						    $ns_content = $item->children('http://purl.org/rss/1.0/modules/content/');
						    if(isset($ns_content) && (isset($ns_content->encoded)))
							$content = (string)$ns_content->encoded;
                            
                            $content .= '[url='.(string) $item->guid.']'.(string) $item->title.'[/url]';
                            $news = array(
                                'userID'       => 0,
                                'username'     => 'FeedPoster',
                                'subject'        => (string) $item->title,
                                'message'   => $content,
                                'time'         => strtotime((string) $item->pubDate),
                                'isDisabled' => (strtotime((string) $item->pubDate) > TIME_NOW)? 1 : 0,
                                'enableBBCodes' => 1,
			                    'enableHtml' => 1,
			                    'enableSmilies' => 1,
                                'imageID' => 0,
                                'lastChangeTime' => TIME_NOW);
                            $categoryIDs = array($feed->categoryID);
                            print_r($news);
                            
                        }
                    }
                    
                }
            }
            
        }
}   