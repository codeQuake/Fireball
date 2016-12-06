<?php

namespace cms\system\page\handler;
use cms\data\page\PageCache;
use wcf\system\page\handler\AbstractLookupPageHandler;
use wcf\system\page\handler\IOnlineLocationPageHandler;
use wcf\system\WCF;

class PagePageHandler extends AbstractLookupPageHandler implements IOnlineLocationPageHandler {
	use TPageOnlineLocationPageHandler;

	/**
	 * @inheritdoc
	 */
	public function getLink($objectID) {
		return PageCache::getInstance()->getPage($objectID)->getLink();
	}

	/**
	 * @inheritdoc
	 */
	public function isValid($objectID) {
		return (PageCache::getInstance()->getPage($objectID) !== null);
	}

	/**
	 * @inheritdoc
	 */
	public function isVisible($objectID = null) {
		$page = PageCache::getInstance()->getPage($objectID);

		if ($page === null)
			return false;

		return (!$page->invisible && $page->canRead());
	}

	/**
	 * @inheritdoc
	 */
	public function lookup($searchString) {
		$sql = "(
			SELECT  pageID, title, title as name
			FROM    wcf" . WCF_N . "_page
			WHERE   title = ?
		)
		UNION
		(
			SELECT  0 as pageID, languageItemValue as title, languageItem as name
			FROM    wcf" . WCF_N . "_language_item
			WHERE   languageID = ?
					AND languageItem LIKE ?
					AND languageItemValue LIKE ?
		)";
		$statement = WCF::getDB()->prepareStatement($sql, 10);
		$statement->execute(array(
			'%' . $searchString . '%',
			WCF::getLanguage()->languageID,
			'cms.page.title%',
			'%' . $searchString . '%'
		));

		$results = array();
		while ($row = $statement->fetchArray()) {
			$pageID = $row['pageID'];
			if ($row['pageID']) {
				if ($row['title'] === 'cms.page.title' . $pageID)
					continue;
			} else {
				if (preg_match('~^cms\.page\.title(\d+)$~', $row['title'], $matches))
					$pageID = $matches[1];
				else
					continue;
			}

			$page = PageCache::getInstance()->getPage($pageID);

			$results[] = array(
				'description' => $page->description,
				'image' => 'fa-text-o',
				'link' => $page->getLink(),
				'objectID' => $pageID,
				'title' => $row['title']
			);
		}

		return $results;
	}
}
