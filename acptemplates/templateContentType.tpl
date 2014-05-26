<dl class="wide">
	<dt><label for="text">{lang}cms.acp.content.type.de.codequake.cms.content.type.template.text{/lang}</label></dt>
	<dd>
		<textarea id="text" rows="20" cols="40" name="contentData[text]">{if $contentData['text']|isset}{$contentData['text']}{/if}</textarea>
		<small>{lang}cms.acp.content.type.de.codequake.cms.content.type.template.text.description{/lang}</small>
    	{include file='codemirror' codemirrorMode='smarty' codemirrorSelector='#text'}
	</dd>
</dl>
