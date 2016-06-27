<fieldset>
	<legend>{lang}cms.acp.page.type.page.comments{/lang}</legend>
	
	<dl{if $errorField == 'isCommentable'} class="formError"{/if}>
		<dt class="reversed"><label for="isCommentable">{lang}cms.acp.page.settings.isCommentable{/lang}</label></dt>
		<dd>
			<input type="checkbox" name="isCommentable" id="isCommentable" value="1"{if $isCommentable == 1} checked="checked"{/if} />
			<small>{lang}cms.acp.page.settings.isCommentable.description{/lang}</small>
		</dd>
	</dl>
</fieldset>
