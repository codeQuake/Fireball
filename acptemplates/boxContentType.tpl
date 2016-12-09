<dl>
	<dt><label for="contentData[boxID]">{lang}cms.acp.content.type.de.codequake.cms.content.type.box.box{/lang}</label></dt>
	<dd>
		<select name="contentData[boxID]" id="contentData[boxID]">
			{foreach from=$boxesByPosition.$position item=box}
				<option value="{$box->boxID}" {if $contentData['boxID']|isset && $box->boxID == $contentData['boxID']}selected="selected"{/if}>{$box->getTitle()}</option>
			{/foreach}
		<select>
	</dd>
</dl>
