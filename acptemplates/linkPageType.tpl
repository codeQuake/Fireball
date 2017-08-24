<section class="section">
	<h2 class="sectionTitle">{lang}cms.acp.page.type.link.general{/lang}</h2>
	
	<dl id="url"{if $errorField == 'url'} class="formError"{/if}>
		<dt><label for="url">{lang}cms.acp.page.settings.url{/lang}</label></dt>
		<dd>
			<input type="url" id="url" name="url" class="long" value="{$url}" />
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
		<dt></dt>
		<dd>
			<input type="checkbox" name="delayedRedirect" id="delayedRedirect" value="1"{if $delayedRedirect == 1} checked="checked"{/if} data-toggle-container="redirectMessage" />
			<label for="delayedRedirect">{lang}cms.acp.page.settings.delayedRedirect{/lang}</label>
			<small>{lang}cms.acp.page.settings.delayedRedirect.description{/lang}</small>
		</dd>
	</dl>
	
	<dl id="delay"{if $errorField == 'url'} class="formError"{/if}>
		<dt><label for="url">{lang}cms.acp.page.settings.delay{/lang}</label></dt>
		<dd>
			<input type="number" min="1" max="60" id="delay" name="delay" class="short" value="{$delay}" />
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
</section>

<script data-relocate="true">
	$(function() {
		$('#delayedRedirect').click(function() {
			var $toggleContainerID = $(this).data('toggleContainer');
			$('#'+ $toggleContainerID).toggle();
		});
	});
</script>
