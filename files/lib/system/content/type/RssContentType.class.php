<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use cms\page\PagePage;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\HTTPRequest;

/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class RssContentType extends AbstractContentType {
	/**
	 * @see	\cms\system\content\type\AbstractContentType::$icon
	 */
	protected $icon = 'icon-rss';

	/**
	 * @see	\cms\system\content\type\IContentType::getOutput()
	 */
	public function getOutput(Content $content) {
		$data = $content->handleContentData();
		$rssURL = $data['url'];
		
		//try {
			$request = new HTTPRequest($rssURL);
			$request->execute();
			$feedData = $request->getReply();
			$feedData = $feedData['body'];
		//}
		//catch (SystemException $e) {
			//// log error
			//$e->getExceptionID();

			//return;
		//}
		
		if (!$xml = simplexml_load_string($feedData)) {
			return;
		}
		$feed = array();
		$i = $data['limit'];
		$feedType = $this->getFeedType($xml);
		$feed = $this->getFeedData($xml, $i, $feedType);
		
		WCF::getTPL()->assign(array(
			'rssFeed' => $feed
		));
		
		return WCF::getTPL()->fetch('rssContentType', 'cms');

	}
	
	public function getFeedType($xml) {
		// get feed type
		if (isset($xml->channel->item)) return 'rss';
		else if (isset($xml->item)) return 'rdf';
		else if (isset($xml->entry)) return 'atom';
		else return null;
	}
	
	public function getFeedData($xml, $i, $feedType) {
		$feed = array();
		switch($feedType) {
			case 'rss':
				foreach ($xml->channel[0]->item as $item) {
					if ($i -- == 0) {
						break;
					}
					
					$dc = $item->children('http://purl.org/dc/elements/1.1/');
					$author = (string) $item->author;
					if ($author == '') (string) $author = $dc->publisher;
					if ($author == '') (string) $author = $dc->creator;
					$feed[] = array(
						'title' => (string) $item->title,
						'description' => (string) $item->description,
						'link' => (string) $item->guid,
						'time' => strtotime((string) $item->pubDate),
						'author' => (string) $author
					);
				}
				break;
			case 'atom':
				foreach ($xml->children()->entry as $item) {
					if ($i -- == 0){
						break;
					}
					$url = $item->link->attributes();
					$feed[] = array(
						'title' => (string) $item->children()->title,
						'description' => (string) $item->summary,
						'link' => (string) $url['href'],
						'time' => strtotime((string) $item->children()->updated),
						'author' => (string) $item->author->name
					);
				}
				break;
				
			case 'rdf':
				foreach ($xml->item as $item) {
					if ($i -- == 0) {
						break;
					}
					
					$feed[] = array(
						'title' => (string) $item->title,
						'description' => (string) $item->description,
						'link' => (string) $item->link,
						'time' => strtotime((string) $item->children()->date),
						'author' => (string) $item->name
					);
				}
				break;
		}
		return $feed;
	}
}
