<fieldset>
	<legend>{lang}cms.acp.page.type.link.general{/lang}</legend>
	
	<dl id="url"{if $errorField == 'url'} class="formError"{/if}>
		<dt><label for="url">{lang}cms.acp.page.settings.url{/lang}</label></dt>
		<dd>
			<input type="url" id="url" name="url" class="long" value="{$url}"></input>
			{if $errorField == 'url'}
				<small class="innerError">
					{if $errorType == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}cms.acp.page.settings.url.error.{@$errorType}{/lang}
					{/if}
				</small>
			{/if}
		</dd>
	</dl>
	
	<dl{if $errorField == 'delayedRedirect'} class="formError"{/if}>
		<dt class="reversed"><label for="delayedRedirect">{lang}cms.acp.page.settings.delayedRedirect{/lang}</label></dt>
		<dd>
			<input type="checkbox" name="delayedRedirect" id="delayedRedirect" value="1"{if $delayedRedirect == 1} checked="checked"{/if} data-toggle-container="redirectMessage" />
			<small>{lang}cms.acp.page.settings.delayedRedirect.description{/lang}</small>
		</dd>
	</dl>
	
	<dl id="delay"{if $errorField == 'url'} class="formError"{/if}>
		<dt><label for="url">{lang}cms.acp.page.settings.delay{/lang}</label></dt>
		<dd>
			<input type="number" min="1" max="60" id="delay" name="delay" class="short" value="{$delay}"></input>
			{if $errorField == 'delay'}
				<small class="innerError">
					{if $errorType == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}cms.acp.page.settings.delay.error.{@$errorType}{/lang}
					{/if}
				</small>
			{/if}
		</dd>
	</dl>
	
	<dl id="redirectMessage"{if $errorField == 'redirectMessage'} class="formError"{/if}{if !$delayedRedirect} style="display: none"{/if}>
		<dt><label for="redirectMessage">{lang}cms.acp.page.settings.redirectMessage{/lang}</label></dt>
		<dd>
			<textarea id="redirectMessage" name="redirectMessage" class="long">{$redirectMessage}</textarea>
			{if $errorField == 'redirectMessage'}
				<small class="innerError">
					{if $errorType == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}cms.acp.page.settings.redirectMessage.error.{@$errorType}{/lang}
					{/if}
				</small>
			{/if}
		</dd>
	</dl>
</fieldset>

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		$('#delayedRedirect').click(function() {
			var $toggleContainerID = $(this).data('toggleContainer');
			$('#'+ $toggleContainerID).toggle();
		});
	});
	//]]>
</script>
