<?php
namespace cms\system\condition;
use cms\data\page\PageCache;
use cms\data\page\PageNodeTree;
use cms\page\ICMSPage;
use wcf\data\condition\Condition;
use wcf\system\condition\AbstractMultiSelectCondition;
use wcf\system\condition\IContentCondition;
use wcf\system\page\PageManager;
use wcf\system\request\RequestHandler;
use wcf\util\ClassUtil;

/**
 * Condition implementation for selecting multiple cms pages.
 *
 * @author	Florian Gail
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class PagePageCondition extends AbstractMultiSelectCondition implements IContentCondition {
	/**
	 * @see	\wcf\system\condition\AbstractSingleFieldCondition::$description
	 */
	protected $description = 'cms.condition.page.page.description';

	/**
	 * @see	\wcf\system\condition\AbstractSelectCondition::$fieldName
	 */
	protected $fieldName = 'cmsPageIDs';

	/**
	 * @see	\wcf\system\condition\AbstractSingleFieldCondition::$label
	 */
	protected $label = 'cms.condition.page.page';

	/**
	 * @see	\wcf\system\condition\AbstractSingleFieldCondition::getFieldElement()
	 */
	protected function getFieldElement() {
		$pageNodeTree = new PageNodeTree();
		$pages = $this->getOptions();
		$pageCount = count($pages);

		$fieldElement = '<select name="'.$this->fieldName.'[]" id="'.$this->fieldName.'" multiple="multiple" size="'.($pageCount > 10 ? 10 : $pageCount).'">';
		/** @var ICMSPage $page */
		foreach ($pageNodeTree as $page) {
			$fieldElement .= '<option value="'.$page->getPage()->pageID.'"'.(in_array($page->getPage()->pageID, $this->fieldValue) ? ' selected="selected"' : '').'>'.str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $page->getDepth() - 1).$page->getPage()->getTitle().'</option>';
		}
		$fieldElement .= "</select>";

		$objectTypes = PageManager::getInstance()->getObjectTypes();
		$pageObjectTypeIDs = array();
		foreach ($objectTypes as $objectType) {
			if (ClassUtil::isInstanceOf($objectType->className, 'cms\page\ICMSPage')) {
				$pageObjectTypeIDs[] = $objectType->objectTypeID;
			}
		}
		$objectTypeIDsString = implode(', ', $pageObjectTypeIDs);

		return <<<HTML
{$fieldElement}
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		new WCF.Condition.PageControllerDependence('cmsPageIDs', [ {$objectTypeIDsString} ]);
	});
	//]]>
</script>
HTML;
	}

	/**
	 * @see	\wcf\system\condition\AbstractSelectCondition::getOptions()
	 */
	protected function getOptions() {
		return PageCache::getInstance()->getPages();
	}

	/**
	 * @see	\wcf\system\condition\IContentCondition::showContent()
	 */
	public function showContent(Condition $condition) {
		$requestObject = RequestHandler::getInstance()->getActiveRequest()->getRequestObject();
		if (!($requestObject instanceof ICMSPage)) return false;

		return in_array($requestObject->getPage()->pageID, $condition->cmsPageIDs);
	}
}
