<dl>
	<dt><label for="contentData[text]">{lang}cms.content.type.de.codequake.cms.content.type.text.text{/lang}</label></dt>
	<dd>
		<textarea name="contentData[text]" id="contentData[text]">{if $contentData['text']|isset}{$contentData['text']}{/if}</textarea>
	</dd>
</dl>

<script data-relocate="true" src="{@$__wcf->getPath('cms')}js/3rdParty/ckeditor/ckeditor.js"></script>
<script data-relocate="true">
	CKEDITOR.replace('contentData[text]');
</script>
