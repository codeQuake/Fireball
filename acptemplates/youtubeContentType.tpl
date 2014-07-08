<dl>
	<dt><label for="contentData[video]">{lang}cms.acp.content.type.de.codequake.cms.content.type.youtube.video{/lang}</label></dt>
	<dd>
		<input type="text" required="required" name="contentData[video]" id="contentData[video]" value="{if $contentData['video']|isset}{$contentData['video']}{/if}" class="long" />
		{if $errorField == 'data[video]'}
			<small class="innerError">
				{if $errorType == 'empty'}{lang}wcf.global.form.error.empty{/lang}
				{elseif $errorType == 'notValid'}{lang}cms.acp.content.type.de.codequake.cms.content.type.youtube.video.error.notValid{/lang}
				{/if}
			</small>
		{/if}
	</dd>
</dl>
