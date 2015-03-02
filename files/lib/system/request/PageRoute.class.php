<?php
namespace cms\system\request;

use cms\data\page\PageCache;
use cms\util\PageUtil;
use wcf\system\request\IRoute;

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
		'controller' => 'page',
		'isDefaultController' => false
	);

	/**
	 * @see	\wcf\system\request\IRoute::buildLink()
	 */
	public function buildLink(array $components) {
		$link = 'page/';

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
			$link = 'index.php?' . $link;
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
		// request URL must be prefixed with `page/`
		if (substr($requestURL, 0, 5) != 'page/') {
			return false;
		}

		// validate alias
		$alias = substr($requestURL, 5, -1);
		if (preg_match('~^' . PageUtil::ALIAS_PATTERN_STACK . '$~', $alias)) {
			$this->routeData['alias'] = $alias;
			return true;
		}

		return false;
	}
}
