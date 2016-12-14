<dl class="wide">
	<dt><label for="text">{lang}cms.acp.content.type.de.codequake.cms.content.type.template.text{/lang}</label></dt>
	<dd>
		<textarea id="text" rows="20" cols="40" name="contentData[text]">{if $contentData['text']|isset}{$contentData['text']}{/if}</textarea>
		{if $errorField == 'text'}
			<small class="innerError">
				{if $errorType == 'empty'}
					{lang}wcf.global.form.error.empty{/lang}
				{else}
					{lang}cms.acp.content.type.de.codequake.cms.content.type.template.text.error.{@$errorType}{/lang}
				{/if}
			</small>
		{/if}
		<small>{lang}cms.acp.content.type.de.codequake.cms.content.type.template.text.description{/lang}</small>

		{if !'ACE_THEME'|defined}
			{include file='codemirror' codemirrorMode='smartymixed' codemirrorSelector='#text'}
		{else}
			{include file='ace' aceMode='smarty' aceSelector='text'}
		{/if}
	</dd>
</dl>
