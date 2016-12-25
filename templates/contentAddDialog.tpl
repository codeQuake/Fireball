<div id="contentAddForm">
	<fieldset>
		<legend>{lang}cms.acp.content.type.{$objectType->objectType}{/lang}</legend>

		{@$typeTemplate}
	</fieldset>
	<fieldset>
		<legend>{lang}cms.acp.content.css{/lang}</legend>

		<dl>
			<dt><label for="cssClasses">{lang}cms.acp.content.css.cssClasses{/lang}</label></dt>
			<dd>
				<input type="text" id="cssClasses" name="cssClasses" value="{$cssClasses}" class="long" />
				<small>{lang}cms.acp.content.css.cssClasses.description{/lang}</small>
			</dd>
		</dl>

		{event name='cssFields'}
	</fieldset>
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" />
		{@SECURITY_TOKEN_INPUT_TAG}
		<input type="hidden" name="position" value="{$position}" />
		<input type="hidden" name="pageID" value="{$pageID}" />
		<input type="hidden" name="objectType" value="{$objectType->objectType}" />
		<input type="hidden" name="parentID" value="{$parentID}" />
	</div>
</div>
