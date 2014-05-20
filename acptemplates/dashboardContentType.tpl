<dl>
	<dt><label for="contentData[box]">{lang}cms.acp.content.type.de.codequake.cms.content.type.dashboard.box{/lang}</label></dt>
	<dd>
		<select name="contentData[box]" id="contentData[box]">
			{foreach from=$boxList item=box}
				{if $box->boxType == 'content' && $position == 'body'}
					<option value="{$box->boxID}" {if $contentData['box']|isset && $box->boxID == $contentData['box']}selected="selected"{/if}>{lang}wcf.dashboard.box.{$box->boxName}{/lang}</option>
				{/if}
				{if $box->boxType == 'sidebar' && $position == 'sidebar'}
					<option value="{$box->boxID}" {if $contentData['box']|isset && $box->boxID == $contentData['box']}selected="selected"{/if}>{lang}wcf.dashboard.box.{$box->boxName}{/lang}</option>
				{/if}
			{/foreach}
		<select>
	</dd>
</dl>
