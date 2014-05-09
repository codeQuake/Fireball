<dl>
	<dt><label for="text">{lang}cms.content.type.de.codequake.cms.content.type.text.text{/lang}</label></dt>
	<dd>
		<textarea name="contentData[text]" id="text">{if $i18nPlainValues['contentData[text]']|isset}{$i18nPlainValues['contentData[text]']}{/if}</textarea>
	</dd>
</dl>

{include file='multipleLanguageInputJavascript' elementIdentifier='text' forceSelection=false}

<script data-relocate="true" src="{@$__wcf->getPath('cms')}js/3rdParty/ckeditor/ckeditor.js"></script>
<script data-relocate="true" src="{@$__wcf->getPath('cms')}js/3rdParty/ckeditor/adapters/jquery.js"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		$('#text').ckeditor();
		
	});
	//]]>
</script>
