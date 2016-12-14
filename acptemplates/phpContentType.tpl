<dl class="wide">
	<dt><label for="text">{lang}cms.acp.content.type.de.codequake.cms.content.type.php{/lang}</label></dt>
	<dd>
		<textarea id="text" rows="20" cols="40" name="contentData[text]">{if $contentData['text']|isset}{$contentData['text']}{/if}</textarea>

		{if !'ACE_THEME'|defined}
			{include file='codemirror' codemirrorMode='php' codemirrorSelector='#text' sandbox=true}
		{else}
			{include file='ace' aceMode='php' aceSelector='text' sandbox=true}
		{/if}
	</dd>
</dl>
