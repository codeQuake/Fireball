<?php
namespace cms\system\condition;
use cms\data\page\PageCache;
use cms\data\page\PageNodeTree;
use cms\page\ICMSPage;
use wcf\data\condition\Condition;
use wcf\data\page\PageCache as WCFPageCache;
use wcf\system\condition\AbstractMultiSelectCondition;
use wcf\system\condition\IContentCondition;
use wcf\system\request\RequestHandler;

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

		$fieldElement = '<select name="'.$this->fieldName.'[]" id="'.$this->fieldName.'" class="medium" multiple="multiple" size="'.($pageCount > 10 ? 10 : $pageCount).'">';
		/** @var \cms\data\page\PageNode $page */
		foreach ($pageNodeTree as $page) {
			$fieldElement .= '<option value="'.$page->pageID.'"'.(in_array($page->pageID, $this->fieldValue) ? ' selected="selected"' : '').'>'.str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $pageNodeTree->getIterator()->getDepth()).$page->getTitle().'</option>';
		}
		$fieldElement .= "</select>";

		$pageIDs = [];
		foreach (WCFPageCache::getInstance()->getPages() as $page) {
			if (is_subclass_of($page->controller, ICMSPage::class)) {
				$pageIDs[] = $page->pageID;
			}
		}
		$pageIDsString = implode(', ', $pageIDs);

		return <<<HTML
{$fieldElement}
<script>
	require(['WoltLabSuite/Core/Controller/Condition/Page/Dependence'], function(ControllerConditionPageDependence) {
		ControllerConditionPageDependence.register(elById('cmsPageIDs').parentNode.parentNode, [ {$pageIDsString} ]);
	});
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
