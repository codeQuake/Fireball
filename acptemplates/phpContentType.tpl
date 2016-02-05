<dl class="wide">
	<dt><label for="text">{lang}fireball.acp.content.type.de.codequake.cms.content.type.php{/lang}</label></dt>
	<dd>
		<textarea id="text" rows="20" cols="40" name="contentData[text]">{if $contentData['text']|isset}{$contentData['text']}{/if}</textarea>

		{include file='codemirror' codemirrorMode='php' codemirrorSelector='#text'}
		<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/codemirror/mode/clike/clike.js"></script>
		<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/codemirror/addon/edit/matchbrackets.js"></script>
		<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/codemirror/mode/xml/xml.js"></script>
		<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/codemirror/mode/css/css.js"></script>
		<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/codemirror/mode/javascript/javascript.js"></script>
		<script data-relocate="true" src="{@$__wcf->getPath()}js/3rdParty/codemirror/mode/htmlmixed/htmlmixed.js"></script>
	</dd>
</dl>
