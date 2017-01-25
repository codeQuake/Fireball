<dl class="wide">
	<dt><label for="text">{lang}cms.acp.content.type.de.codequake.cms.content.type.text.text{/lang}</label></dt>
	<dd>
		<textarea name="text" id="text">{$i18nPlainValues['text']}</textarea>

		{include file='wysiwyg'}
		{include file='wysiwygI18n' elementIdentifier='text' forceSelection=false}

		<input type="hidden" id="contentData[enableHtml]" name="contentData[enableHtml]" value="1" />
	</dd>
</dl>

