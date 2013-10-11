{include file='multipleLanguageInputJavascript' elementIdentifier='sectionData' forceSelection=false}
<dl>
	<dt><label for="hlType">{lang}cms.acp.content.section.data.hlType{/lang}</label></dt>
	<dd>
		<select id="hlType" name="hlType">
			<option value="h1" {if $hlType == 'h1'}selected="selected"{/if}>{lang}cms.acp.content.section.data.h1{/lang}</option>
			<option value="h2" {if $hlType == 'h2'}selected="selected"{/if}>{lang}cms.acp.content.section.data.h2{/lang}</option>
			<option value="h3" {if $hlType == 'h3'}selected="selected"{/if}>{lang}cms.acp.content.section.data.h3{/lang}</option>
			<option value="h4" {if $hlType == 'h4'}selected="selected"{/if}>{lang}cms.acp.content.section.data.h4{/lang}</option>
		</select>
	</dd>
</dl>
<dl>
    <dt><label for="sectionData">{lang}cms.acp.content.section.data.headline{/lang}</label></dt>
    <dd>
		<input type="text" name="sectionData" id="sectionData" class="long" value="{$i18nPlainValues['sectionData']}" required="required" />
    </dd>
</dl>