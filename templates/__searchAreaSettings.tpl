{if $__cms->isActiveApplication() && $__searchAreaInitialized|empty}
	{capture assign='__searchInputPlaceholder'}{lang}cms.news.searchNews{/lang}{/capture}
	{capture assign='__searchHiddenInputFields'}<input type="hidden" name="types[]" value="de.codequake.cms.news" />{/capture}
{/if}