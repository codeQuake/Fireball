<?php
namespace cms\system\request;

use cms\data\page\PageCache;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\request\route\IRequestRoute;
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
class PageRoute implements IRequestRoute {
	/**
	 * parsed request data
	 * @var	array<mixed>
	 */
	protected $routeData = array(
		'application' => 'cms',
		'controller' => 'page',
		'isDefaultController' => false
	);

	/**
	 * @see	\wcf\system\request\IRoute::buildLink()
	 */
	public function buildLink(array $components) {
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
		$page = PageCache::getInstance()->getPage(PageCache::getInstance()->getIDByAlias($this->routeData['alias']));
		if ($page !== null) {
			$classParts = explode('\\', $page->getObjectType()->pageclass);
			$className = array_pop($classParts);
			$pageController = lcfirst($className);
			$controller = $this->getControllerName($className);
			
			if ($controller != $pageController || $this->routeData['controller'] != $pageController)
				$this->routeData['controller'] = $controller;
		}
		
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
		
		if (!URL_LEGACY_MODE) {
			// request URL must be prefixed with `page/`
			if (substr($requestURL, 0, strlen($controller) + 1) != $controller . '/' && substr($requestURL, 0, 5) != 'page/' && !empty($requestURL)) {
				return false;
			}
			
			$availableControllers = $this->getSupportedControllers();
			foreach ($availableControllers as $supportedController) {
				if (substr($requestURL, 0, 5) == $supportedController . '/' && $controller != $supportedController) {
					$alias = substr($requestURL, 5, -1);
					HeaderUtil::redirect($this->buildLink(array('alias' => $alias)), true);
					exit;
				}
			}
			
			$alias = substr($requestURL, strlen($controller) + 1);
			$alias = trim($alias, '/');
		} else {
			$alias = trim($requestURL, '/');
		}
		
		// validate alias
		if (!empty($alias) && preg_match('~^' . '([a-z0-9]+((?:[a-z0-9-]+)*)*\/?)*' . '$~', $alias)) {
			$this->routeData['alias'] = $alias;
			return true;
		}

		if ($this->landingPage === null)
			$this->landingPage = PageMenu::getInstance()->getLandingPage();
		$processor = $this->landingPage->getProcessor();
		if (empty($_GET['ajax-proxy']) && empty($_GET['t']) && empty($_POST['actionName']) && empty($_POST['className']) && empty($_GET['alias']) && $processor instanceof CMSPageMenuItemProvider) {
			$page = $processor->getPage();
			$alias = $page->getAlias();
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
	
	protected function getSupportedControllers() {
		$types = ObjectTypeCache::getInstance()->getObjectTypes('de.codequake.page.type');
		$controllers = array();
		foreach ($types as $type) {
			$controllers[] = $type->pageclass;
		}
		
		return $controllers;
	}

	/**
	 * Configures this route to handle either ACP or frontend requests.
	 *
	 * @param    boolean $isACP true if route handles ACP requests
	 */
	public function setIsACP($isACP) {
		$this->isACP = $isACP;
	}
}
