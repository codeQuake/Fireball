<dl>
	<dt><label for="contentData[type]">{lang}cms.acp.content.type.de.codequake.cms.content.type.link.type{/lang}</label></dt>
	<dd>
		<select name="contentData[type]" id="contentData[type]">
			<option value="link" {if $contentData['type']|isset && $contentData['type'] == 'link'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.link.type.link{/lang}</option>
			<option value="button" {if $contentData['type']|isset && $contentData['type'] == 'button'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.link.type.button{/lang}</option>
			<option value="smallbutton" {if $contentData['type']|isset && $contentData['type'] == 'smallbutton'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.link.type.smallbutton{/lang}</option>
		<select>
	</dd>
</dl>

<dl>
	<dt><label for="text">{lang}cms.acp.content.type.de.codequake.cms.content.type.link.text{/lang}</label></dt>
	<dd>
		<input name="text" id="text" type="text" value="{$i18nPlainValues['text']}"  class="long" required="required" />

		{include file='multipleLanguageInputJavascript' elementIdentifier='text' forceSelection=false}
	</dd>
</dl>

<dl>
	<dt><label for="link">{lang}cms.acp.content.type.de.codequake.cms.content.type.link.hyperlink{/lang}</label></dt>
	<dd>
		<input name="link" id="link" type="text" value="{$i18nPlainValues['link']}"  class="long" required="required" />

		{include file='multipleLanguageInputJavascript' elementIdentifier='link' forceSelection=false}
	</dd>
</dl>
