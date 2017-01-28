<dl class="wide">
	<dt><label for="text">{lang}cms.acp.content.type.de.codequake.cms.content.type.text.text{/lang}</label></dt>
	<dd>
		<textarea name="text" id="text">{$i18nPlainValues['text']}</textarea>
		{if !$errorField|empty && $errorField == 'text'}
			<small class="innerError">
				{if $errorType == 'empty'}
					{lang}wcf.global.form.error.empty{/lang}
				{elseif $errorType == 'tooLong'}
					{lang}wcf.message.error.tooLong{/lang}
				{elseif $errorType == 'censoredWordsFound'}
					{lang}wcf.message.error.censoredWordsFound{/lang}
				{elseif $errorType == 'disallowedBBCodes'}
					{lang}wcf.message.error.disallowedBBCodes{/lang}
				{else}
					{lang}cms.content.text.text.error.{@$errorType}{/lang}
				{/if}
			</small>
		{/if}

		{include file='wysiwyg'}
		{include file='wysiwygI18n' elementIdentifier='text' forceSelection=false}

		<input type="hidden" id="contentData[enableHtml]" name="contentData[enableHtml]" value="1" />
	</dd>
</dl>
