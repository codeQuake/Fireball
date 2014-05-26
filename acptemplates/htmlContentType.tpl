<dl class="wide">
	<dt><label for="contentData[text]">{lang}cms.acp.content.type.de.codequake.cms.content.type.html.text{/lang}</label></dt>
	<dd>
		<textarea id="contentData[text]" rows="20" cols="40" name="contentData[text]">{if $contentData['text']|isset}{$contentData['text']}{/if}</textarea>
		<small>{lang}cms.acp.content.type.de.codequake.cms.content.type.html.text.description{/lang}</small>
    	{include file='codemirror' codemirrorMode='html' codemirrorSelector='#contentData[text]'}
	</dd>
</dl>
