<div id="contentAddForm">
	<section class="section">
		<h2 class="sectionTitle">{lang}cms.acp.content.type.{$objectType->objectType}{/lang}</h2>

		{@$typeTemplate}
	</section>

	<section class="section">
		<h2 class="sectionTitle">{lang}cms.acp.content.css{/lang}</h2>

		<dl>
			<dt><label for="cssClasses">{lang}cms.acp.content.css.cssClasses{/lang}</label></dt>
			<dd>
				<input type="text" id="cssClasses" name="cssClasses" value="{$cssClasses}" class="long" />
				<small>{lang}cms.acp.content.css.cssClasses.description{/lang}</small>
			</dd>
		</dl>

		{event name='cssFields'}
	</section>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" />
		{@SECURITY_TOKEN_INPUT_TAG}
		<input type="hidden" name="position" value="{$position}" />
		<input type="hidden" name="pageID" value="{$pageID}" />
		<input type="hidden" name="objectType" value="{$objectType->objectType}" />
		<input type="hidden" name="parentID" value="{$parentID}" />
	</div>
</div>
