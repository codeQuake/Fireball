<script data-relocate="true">
	//<![CDATA[
	$(function() {
		new WCF.Search.User('#username');
	});
	//]]>
</script>

<dl{if $errorField == 'data[name]'} class="formError"{/if}>
	<dt><label for="username">{lang}cms.acp.content.type.de.codequake.cms.content.type.user.name{/lang}</label></dt>
	<dd>
		<input type="text" name="contentData[name]" id="username" class="medium" required="required" value="{if $contentData['name']|isset}{$contentData['name']}{/if}"/>
		{if $errorField == 'data[name]'}
			<small class="innerError">
				{if $errorType == 'empty'}
					{lang}wcf.global.form.error.empty{/lang}
				{elseif $errorType == 'notValid'}
					{lang}wcf.user.username.error.notValid{/lang}
				{/if}
			</small>
		{/if}
	</dd>
</dl>
