<dl>
	<dt><label for="contentData[type]">{lang}cms.acp.content.type.de.codequake.cms.content.type.headline.type{/lang}</label></dt>
	<dd>
		<select name="contentData[type]" id="contentData[type]">
			<option value="h1" {if $contentData['type']|isset && $contentData['type'] == 'h1'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.headline.type.h1{/lang}</option>
			<option value="h2" {if $contentData['type']|isset && $contentData['type'] == 'h2'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.headline.type.h2{/lang}</option>
			<option value="h3" {if $contentData['type']|isset && $contentData['type'] == 'h3'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.headline.type.h3{/lang}</option>
			<option value="h4" {if $contentData['type']|isset && $contentData['type'] == 'h4'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.headline.type.h4{/lang}</option>
			<option value="h5" {if $contentData['type']|isset && $contentData['type'] == 'h5'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.headline.type.h5{/lang}</option>
		<select>
	</dd>
</dl>

<dl>
	<dt><label for="text">{lang}cms.acp.content.type.de.codequake.cms.content.type.headline.text{/lang}</label></dt>
	<dd>
		<input name="text" id="text" type="text" value="{$i18nPlainValues['text']}"  class="long" required="required" />
	</dd>
</dl>

{include file='multipleLanguageInputJavascript' elementIdentifier='text' forceSelection=false}
