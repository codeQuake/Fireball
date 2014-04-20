<?php
namespace cms\system\request;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class Route extends \wcf\system\request\Route {
    
    // page alias route ;)
    protected function getParts($requestURL) {
        $urlParts = explode('/', $requestURL);
        foreach ($urlParts as $index => $part) {
            if (empty($part)) {
                unset($urlParts[$index]);
            }
        }
        
        $urlParts = array_values($urlParts);
        return array(
            implode('/', $urlParts)
        );
    }

    public function buildLink(array $components) {
        if (isset($components['controller'])) {
            unset($components['controller']);
        }
        
        return parent::buildLink($components);
    }
}
