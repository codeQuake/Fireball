<dl>
	<dt><label for="contentData[box]">{lang}fireball.acp.content.type.de.codequake.cms.content.type.dashboard.box{/lang}</label></dt>
	<dd>
		<select name="contentData[box]" id="contentData[box]">
			{foreach from=$boxList item=box}
				{if $box->boxType == 'content' && $position == 'body'}
					<option value="{$box->boxID}" {if $contentData['box']|isset && $box->boxID == $contentData['box']}selected="selected"{/if}>{lang}wcf.dashboard.box.{$box->boxName}{/lang}{if $box->packageID != 1} ({$box->getPackage()->packageName|language}){/if}</option>
				{/if}
				{if $box->boxType == 'sidebar' && $position == 'sidebar'}
					<option value="{$box->boxID}" {if $contentData['box']|isset && $box->boxID == $contentData['box']}selected="selected"{/if}>{lang}wcf.dashboard.box.{$box->boxName}{/lang}{if $box->packageID != 1} ({$box->getPackage()->packageName|language}){/if}</option>
				{/if}
			{/foreach}
		<select>
	</dd>
</dl>
