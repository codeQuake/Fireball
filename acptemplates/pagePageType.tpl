<section class="section">
	<h2 class="sectionTitle">{lang}cms.acp.page.type.page.comments{/lang}</h2>
	
	<dl{if $errorField == 'isCommentable'} class="formError"{/if}>
		<dt class="reversed"><label for="isCommentable">{lang}cms.acp.page.settings.isCommentable{/lang}</label></dt>
		<dd>
			<input type="checkbox" name="isCommentable" id="isCommentable" value="1"{if $isCommentable == 1} checked="checked"{/if} />
			<small>{lang}cms.acp.page.settings.isCommentable.description{/lang}</small>
		</dd>
	</dl>
</section>
