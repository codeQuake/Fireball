<div id="contentAddForm">
	<fieldset>
			<legend>{lang}fireball.acp.content.type.{$objectType->objectType}{/lang}</legend>

			{include file=$objectType->getProcessor()->getFormTemplate()|concat:'Form' application='cms'}
	</fieldset>
	<fieldset>
			<legend>{lang}fireball.acp.content.css{/lang}</legend>

			<dl>
				<dt><label for="cssClasses">{lang}fireball.acp.content.css.cssClasses{/lang}</label></dt>
				<dd>
					<input type="text" id="cssClasses" name="cssClasses" value="{$cssClasses}" class="long" />
					<small>{lang}fireball.acp.content.css.cssClasses.description{/lang}</small>
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