<?php

namespace wcf\system\request\route;

use cms\data\page\Page;
use cms\data\page\PageCache;
use wcf\data\application\Application;
use wcf\data\package\PackageCache;
use wcf\data\page\PageCache as WCFPageCache;
use wcf\system\application\ApplicationHandler;
use wcf\util\FileUtil;

class FireballRequestRoute implements IRequestRoute {
	/**
	 * list of parsed route information
	 * @var	array
	 */
	protected $routeData = [];

	/**
	 * Set's the route data for the given fireball page
	 * @param Page $cmsPage
	 */
	protected function setRouteData(Page $cmsPage) {
		$this->routeData = [
			'className' => $cmsPage->getProcessor()->frontendController,
			'controller' => 'page',
			'pageType' => 'system',
			'id' => $cmsPage->pageID,
			'cmsPageID' => $cmsPage->wcfPageID,
			'cmsPageLanguageID' => null,
			'isDefaultController' => false
		];
	}

	/**
	 * @inheritDoc
	 */
	public function matches($requestURL) {
		//if (RequestHandler::getInstance()->isACPRequest() || isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) return false;
		$requestURL = FileUtil::removeLeadingSlash($requestURL);

		if ($requestURL === '') {
			$cms = PackageCache::getInstance()->getPackageByIdentifier('de.codequake.cms');
			$activeApplication = new Application(PACKAGE_ID);
			$fireballPageID = PageCache::getInstance()->getFireballPageID($activeApplication->landingPageID);

			if ($fireballPageID) {
				$this->setRouteData(PageCache::getInstance()->getPage($fireballPageID));
				return true;
			}
			else if (PACKAGE_ID == $cms->packageID) {
				$root = PageCache::getInstance()->getHomePage();
				$rootAbs = WCFPageCache::getInstance()->getLandingPage();

				if ($rootAbs->getApplication()->getAbbreviation() == 'cms' && $rootAbs->originIsSystem == 0) {
					$this->setRouteData($root);
					return true;
				} else {
					// ignore empty urls and let them be handled by regular routes
					return false;
				}
			}
		}

		$regex = '~^(
			(
				(?P<controller>.+?)
				(?:
					\/(?P<id>[0-9]+)
					(?:
						-
						(?P<title>[^/]+)
					)?
					/?
				)
			)
		|
			(
				(?:
					(?P<alias>.*)
					/?
				)
			)
		)$~x';

		if (preg_match($regex, $requestURL, $matches)) {
			$alias = '';
			$fireballLandingPageID = PageCache::getInstance()->getFireballPageID(ApplicationHandler::getInstance()->getActiveApplication()->landingPageID);
			$fireballLandingPage = PageCache::getInstance()->getPage($fireballLandingPageID);
			if ($fireballLandingPage && $fireballLandingPage->alias) $alias = $fireballLandingPage->alias . '/';


			$pageList = PageCache::getInstance()->getPages();
			$urlList = [];
			foreach ($pageList as $page) {
				$urlList[$page->getAlias()] = $page;
			}

			if (!empty($matches['alias'])) {
				$alias = FileUtil::removeTrailingSlash($alias . $matches['alias']);
				if (!empty($urlList[$alias])) {
					$this->setRouteData($urlList[$alias]);
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function getRouteData() {
		return $this->routeData;
	}

	/**
	 * @inheritDoc
	 */
	public function setIsACP($isACP) {
		throw new \BadMethodCallException('lookups are not supported for ACP requests');
	}

	/**
	 * @inheritDoc
	 * @throws	\BadMethodCallException
	 */
	public function buildLink(array $components) {
		$page = null;

		if (!empty($components['id'])) {
			$page = PageCache::getInstance()->getPage($components['id']);
		}
		else if (!empty($components['alias'])) {
			$pageID = PageCache::getInstance()->getIDByAlias($components['alias']);
			$page = PageCache::getInstance()->getPage($pageID);
		}
		else if (empty($components['id']) && empty($components['alias'])) {
			$page = PageCache::getInstance()->getHomePage();
		}

		if ($page === null || !$page->pageID) {
			return '';
		}

		return FileUtil::addTrailingSlash($page->getAlias());
	}

	/**
	 * @inheritDoc
	 */
	public function canHandle(array $components) {
		return ($components['application'] == 'cms' && $components['controller'] == 'Page');
	}

	/**
	 * @inheritDoc
	 */
	public function isACP() {
		// lookups are not supported for ACP requests
		return false;
	}
}
