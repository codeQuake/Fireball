<?php
namespace cms\system\request;

use cms\data\page\PageCache;
use cms\system\menu\page\CMSPageMenuItemProvider;
use cms\util\PageUtil;
use wcf\system\menu\page\PageMenu;
use wcf\system\request\IRoute;
use wcf\system\request\RequestHandler;
use wcf\util\HeaderUtil;

/**
 * Route implementation for cms pages.
 * Schema: `page/{alias-stack}`
 * 
 * @author	Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PageRoute implements IRoute {
	/**
	 * parsed request data
	 * @var	array<mixed>
	 */
	protected $routeData = array(
		'application' => 'cms',
		'controller' => 'page',
		'isDefaultController' => false
	);
	
	protected $landingPage = null;

	/**
	 * @see	\wcf\system\request\IRoute::buildLink()
	 */
	public function buildLink(array $components) {
		$this->landingPage = PageMenu::getInstance()->getLandingPage();
		$processor = $this->landingPage->getProcessor();
		if ($processor instanceof CMSPageMenuItemProvider) {
			$page = $processor->getPage();
			if (isset($components['alias']) && $components['alias'] == $page->alias)
				return '';
			if (isset($components['id']) && $components['id'] == $page->pageID)
				return '';
		}
		
		$link = '';
		if (!URL_LEGACY_MODE) {
			$link = $this->getControllerName() . '/';
		}

		$alias = (isset($components['alias'])) ? $components['alias'] : '';

		// get alias of page with the given id
		if ($alias == '' && isset($components['id'])) {
			$page = PageCache::getInstance()->getPage($components['id']);
			if ($page !== null) {
				$alias = $page->getAlias();
			}
		}

		if (!empty($alias)) {
			$link .= $alias . '/';
		}

		// unset special components to prevent appending later
		foreach (array('alias', 'application', 'controller', 'id') as $componentName) {
			if (isset($components[$componentName])) {
				unset($components[$componentName]);
			}
		}

		// prepend index.php
		if (!URL_OMIT_INDEX_PHP) {
			if (URL_LEGACY_MODE) {
				$link = 'index.php/' . $link;
			} else {
				$link = 'index.php?' . $link;
			}
		}

		if (!empty($components)) {
			if (strpos($link, '?') === false) $link .= '?';
			else $link .= '&';

			$link .= http_build_query($components, '', '&');
		}

		return $link;
	}

	/**
	 * Returns true, if build request points to `\cms\page\PagePage` and
	 * either a page id or a page alias is given.
	 * 
	 * @see	\wcf\system\request\IRoute::canHandle()
	 */
	public function canHandle(array $components) {
		if (!isset($components['application']) || $components['application'] != 'cms') {
			// doesn't point to cms => not our business
			return false;
		}

		if ($components['controller'] != 'Page') {
			// doesn't point to a page
			return false;
		}

		if (!isset($components['id']) && !isset($components['alias'])) {
			// nether a page id nor a page alias given
			return false;
		}

		return true;
	}

	/**
	 * @see	\wcf\system\request\IRoute::getRouteData()
	 */
	public function getRouteData() {
		$controller = $this->getControllerName();

		if ($controller != 'page')
			$this->routeData['controller'] = $controller;

		return $this->routeData;
	}

	/**
	 * @see	\wcf\system\request\IRoute::isACP()
	 */
	public function isACP() {
		return false;
	}

	/**
	 * @see	\wcf\system\request\IRoute::matches()
	 */
	public function matches($requestURL) {
		$controller = $this->getControllerName();
		
		$this->landingPage = PageMenu::getInstance()->getLandingPage();
		$processor = $this->landingPage->getProcessor();
		if ($processor instanceof CMSPageMenuItemProvider) {
			$page = $processor->getPage();
			$alias = $page->getAlias();
			$this->routeData['alias'] = $alias;
			return true;
		}

		if (!URL_LEGACY_MODE) {
			// request URL must be prefixed with `page/`
			if (substr($requestURL, 0, strlen($controller) + 1) != $controller . '/' && substr($requestURL, 0, 5) != 'page/') {
				return false;
			}

			if (substr($requestURL, 0, 5) == 'page/' && $controller != 'page') {
				$alias = substr($requestURL, 5, -1);
				HeaderUtil::redirect($this->buildLink(array('alias' => $alias)), true);
				exit;
			}

			$alias = substr($requestURL, strlen($controller) + 1);
			$alias = trim($alias, '/');
		} else {
			$alias = trim($requestURL, '/');
		}

		// validate alias
		if (preg_match('~^' . PageUtil::ALIAS_PATTERN_STACK . '$~', $alias)) {
			$this->routeData['alias'] = $alias;
			return true;
		}

		return false;
	}
	
	/**
	 * Returns the transformed controller name.
	 *
	 * @param	string		$application
	 * @param	string		$controller
	 * @return	string
	 */
	protected function getControllerName($controller = 'Page') {
		if (!isset($this->controllerNames[$controller])) {
			$controllerName = RequestHandler::getTokenizedController($controller);
			$alias = RequestHandler::getInstance()->getAliasByController($controllerName);
				
			$this->controllerNames[$controller] = ($alias) ?: $controllerName;
		}

		return $this->controllerNames[$controller];
	}
}
