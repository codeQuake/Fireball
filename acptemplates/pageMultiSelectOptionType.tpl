<select id="{$option->optionName}" name="values[{$option->optionName}][]" multiple="multiple" size="10">
	{foreach from=$nodeList item=page}
		<option value="{@$page->pageID}"{if $page->pageID|in_array:$value} selected="selected"{/if}>{@'&nbsp;&nbsp;&nbsp;&nbsp;'|str_repeat:$nodeList->getDepth()}{$page->getTitle()}</option>
	{/foreach}
</select>
