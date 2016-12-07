<?php

namespace wcf\system\request\route;

use cms\page\PagePage;
use wcf\data\page\PageCache;

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

		$regex = '~^
			(?P<controller>.+?)
			(?:
				(?P<id>[0-9]+)
				(?:
					-
					(?P<title>[^/]+)
				)?
				/
			)?
		$~x';

		if (preg_match($regex, $requestURL, $matches)) {
			$application = ApplicationHandler::getInstance()->getActiveApplication()->getAbbreviation();

			if ($application == 'cms') {
				$pageList = PageCache::getInstance()->getPages();
				$urlList = array();
				foreach ($pageList as $page) {
					$urlList[$page->alias] = $page;
				}

				/*
				$routeData = array(
					'className' => PagePage::class,
					'controller' => 'page',
					'pageType' => 'system',

					// CMS page meta data
					'cmsPageID' => $matches['pageID'],
					'cmsPageLanguageID' => $matches['languageID']
				);
				*/
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
