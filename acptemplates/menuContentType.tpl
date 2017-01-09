<dl>
	<dt><label for="contentData[type]">{lang}cms.acp.content.type.de.codequake.cms.content.type.menu.type{/lang}</label></dt>
	<dd>
		<select name="contentData[type]" id="contentData[type]">
			<option value="children"{if !$contentData['type']|empty && $contentData['type'] == 'children'} selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.menu.type.children{/lang}</option>
			<option value="all"{if !$contentData['type']|empty && $contentData['type'] == 'all'} selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.menu.type.all{/lang}</option>
		</select>
	</dd>
</dl>

<dl>
	<dt><label for="contentData[pageID]">{lang}cms.acp.content.type.de.codequake.cms.content.type.menu.pageID{/lang}</label></dt>
	<dd>
		<select name="contentData[pageID]" id="contentData[pageID]">
			{foreach from=$pageList item=$node}
				<option{if !$contentData['pageID']|empty && $node->pageID == $contentData['pageID']} selected="selected"{/if} value="{@$node->pageID}">{@"&nbsp;&nbsp;&nbsp;&nbsp;"|str_repeat:$pageList->getDepth()}{$node->getTitle()}</option>
			{/foreach}
		</select>
	</dd>
</dl>

<dl>
	<dt><label for="contentData[depth]">{lang}cms.acp.content.type.de.codequake.cms.content.type.menu.depth{/lang}</label></dt>
	<dd>
		<input type="number" name="contentData[depth]" id="contentData[depth]" value="{if $contentData['depth']|isset}{$contentData['depth']}{else}0{/if}"/>
		<small>{lang}cms.acp.content.type.de.codequake.cms.content.type.menu.depth.description{/lang}</small>
	</dd>
</dl>
