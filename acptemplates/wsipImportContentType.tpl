<dl class="wide">
	<dt><label for="text">{lang}cms.acp.content.type.de.codequake.cms.content.type.template.text{/lang}</label></dt>
	<dd>
		<textarea id="text" rows="20" cols="40" name="contentData[text]"></textarea>
		<textarea id="html" style="display: none;">{if $contentData['text']|isset}{$contentData['text']}{/if}</textarea>
		{if $errorField == 'text'}
			<small class="innerError">
				{if $errorType == 'empty'}
					{lang}wcf.global.form.error.empty{/lang}
				{else}
					{lang}cms.acp.content.type.de.codequake.cms.content.type.wsipimport.text.error.{@$errorType}{/lang}
				{/if}
			</small>
		{/if}
	</dd>
</dl>

{include file='wysiwyg'}

<script data-relocate="true">
		$(function() {
			console.l
			WCF.System.Dependency.Manager.register('Redactor_text', function() {
				$converted = $('#text').redactor('wbbcode.convertFromHtml', $('#html').val());
				$('#text').redactor('wutil.replaceText', $converted);
			});
		});
</script>
