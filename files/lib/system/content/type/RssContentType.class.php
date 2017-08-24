<?php
namespace cms\system\content\type;

use cms\data\content\Content;
use wcf\system\exception\SystemException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HTTPRequest;

/**
 * @author	Jens Krumsieck
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class RssContentType extends AbstractContentType {
	/**
	 * @inheritDoc
	 */
	protected $icon = 'fa-rss';
	
	/**
	 * @inheritDoc
	 */
	protected $previewFields = ['url'];

	/**
	 * @inheritDoc
	 */
	public function getOutput(Content $content) {
		try {
			$request = new HTTPRequest($content->url);
			$request->execute();
			$feedData = $request->getReply();
			$feedData = $feedData['body'];
		}
		catch (SystemException $e) {
			if ($content->getPermission('mod.canViewErroredContent')) {
				$url = LinkHandler::getInstance()->getLink('ContentEdit', ['application' => 'cms', 'object' => $content, 'isACP' => true]);
				return '<div class="error">Please check <a href="' . $url . '">content #' . $content->contentID . '</a>. The following error occurred fetching the feed from <span class="inlineCode">' . $content->url . '</span>:<br><br>' . $e->getMessage() . '</div>';
			} else {
				return '';
			}
		}
		
		if (!$xml = simplexml_load_string($feedData)) {
			return '';
		}
		$feedType = $this->getFeedType($xml);
		
		WCF::getTPL()->assign([
			'rssFeed' => $this->getFeedData($xml, $content->limit, $feedType)
		]);
		
		return parent::getOutput($content);

	}

	/**
	 * @inheritDoc
	 */
	public function getSortableOutput(Content $content) {
		return '<span class="inlineCode"></span>' . $content->url . '<span>';
	}
	
	public function getFeedType($xml) {
		// get feed type
		if (isset($xml->channel->item)) return 'rss';
		else if (isset($xml->item)) return 'rdf';
		else if (isset($xml->entry)) return 'atom';
		else return null;
	}
	
	public function getFeedData($xml, $i, $feedType) {
		$feed = [];

		switch($feedType) {
			case 'rss':
				foreach ($xml->channel[0]->item as $item) {
					if ($i -- == 0) {
						break;
					}
					
					$dc = $item->children('http://purl.org/dc/elements/1.1/');
					$author = (string) $item->author;
					if ($author == '') $author = (string) $dc->publisher;
					if ($author == '') $author = (string) $dc->creator;
					$feed[] = [
						'title' => (string) $item->title,
						'description' => (string) $item->description,
						'link' => (string) $item->guid,
						'time' => strtotime((string) $item->pubDate),
						'author' => (string) $author
					];
				}
				break;
			case 'atom':
				foreach ($xml->children()->entry as $item) {
					if ($i -- == 0) {
						break;
					}
					$url = $item->link->attributes();
					$feed[] = [
						'title' => (string) $item->children()->title,
						'description' => (string) $item->summary,
						'link' => (string) $url['href'],
						'time' => strtotime((string) $item->children()->updated),
						'author' => (string) $item->author->name
					];
				}
				break;
				
			case 'rdf':
				foreach ($xml->item as $item) {
					if ($i -- == 0) {
						break;
					}
					
					$feed[] = [
						'title' => (string) $item->title,
						'description' => (string) $item->description,
						'link' => (string) $item->link,
						'time' => strtotime((string) $item->children()->date),
						'author' => (string) $item->name
					];
				}
				break;
		}

		return $feed;
	}
}
