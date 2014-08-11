<select id="{$option->optionName}" name="values[{$option->optionName}]">
	<option value="0">{lang}wcf.global.noSelection{/lang}</option>
	{foreach from=$nodeList item=node}
		<option value="{@$node->pageID}"{if $node->pageID == $value} selected="selected"{/if}>{@'&nbsp;&nbsp;&nbsp;&nbsp;'|str_repeat:$nodeList->getDepth()}{$node->getTitle()}</option>
	{/foreach}
</select>
