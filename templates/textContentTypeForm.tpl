<dl class="wide">
	<dt><label for="text">{lang}fireball.acp.content.type.de.codequake.cms.content.type.text.text{/lang}</label></dt>
	<dd>
		<textarea name="contentData[text]" id="text">{if $contentData['text']|isset}{$contentData['text']}{/if}</textarea>

		{*include file='multipleLanguageWysiwygJavascript' elementIdentifier='text' forceSelection=false*}
	</dd>
</dl>

{include file='wysiwyg'}
