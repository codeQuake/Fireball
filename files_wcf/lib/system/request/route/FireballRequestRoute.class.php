<?php

namespace wcf\system\request\route;

use cms\data\page\PageCache;
use cms\page\PagePage;
use wcf\system\application\ApplicationHandler;
use wcf\util\FileUtil;

class FireballRequestRoute implements IRequestRoute {
	/**
	 * list of parsed route information
	 * @var	array
	 */
	protected $routeData = [];

	/**
	 * @inheritDoc
	 */
	public function matches($requestURL) {
		$requestURL = FileUtil::removeLeadingSlash($requestURL);

		if ($requestURL === '') {
			// ignore empty urls and let them be handled by regular routes
			return false;
		}

		$regex = '~^(
			(
				(?P<controller>.+?)
				(?:
					(?P<id>[0-9]+)
					(?:
						-
						(?P<title>[^/]+)
					)?
					/
				)
			)
		|
			(
				(?:
					(?P<alias>.*)
					/
				)
			)
		)$~x';

		if (preg_match($regex, $requestURL, $matches)) {
			$application = ApplicationHandler::getInstance()->getActiveApplication()->getAbbreviation();

			if ($application == 'cms') {
				$pageList = PageCache::getInstance()->getPages();
				$urlList = [];
				foreach ($pageList as $page) {
					$urlList[$page->getAlias()] = $page;
				}

				if (!empty($matches['alias'])) {
					$alias = FileUtil::removeTrailingSlash($matches['alias']);
					if (!empty($urlList[$alias])) {
						$this->routeData = [
							'className' => $urlList[$alias]->getProcessor()->frontendController,
							'controller' => 'page',
							'pageType' => 'system',
							'id' => $urlList[$alias]->pageID,
							'cmsPageID' => $urlList[$alias]->wcfPageID,
							'cmsPageLanguageID' => null
						];
					}
				} else if (empty($matches['id'])) {
					if (preg_match('~(([A-Za-z0-9]+)/?){0,}$~x', $requestURL, $urlParts)) {
						$alias = FileUtil::removeTrailingSlash($urlParts[0]);

						if (!empty($urlList[$alias])) {
							$this->routeData = [
								'className' => $urlList[$alias]->getProcessor()->frontendController,
								'controller' => 'page',
								'pageType' => 'system',
								'id' => $urlList[$alias]->pageID,
								'cmsPageID' => $urlList[$alias]->wcfPageID,
								'cmsPageLanguageID' => null
							];
						}
					}
				}

				if (!empty($this->routeData)) {
					$this->routeData['isDefaultController'] = false;

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
		throw new \BadMethodCallException('LookupRequestRoute cannot build links, please verify capabilities by calling canHandle() first.');
	}

	/**
	 * @inheritDoc
	 */
	public function canHandle(array $components) {
		// this route cannot build routes, it is a one-way resolver
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function isACP() {
		// lookups are not supported for ACP requests
		return false;
	}
}
