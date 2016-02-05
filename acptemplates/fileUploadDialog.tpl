<div id="fileUpload" class="marginTop fileUpload">
	<dl>
		<dt>{lang}fireball.acp.file.files{/lang}</dt>
		<dd>
			<ul class="formAttachmentList clearfix"></ul>
			<div id="fileUploadButton"></div>
			<small class="marginTopSmall">{lang}fireball.acp.file.files.description{/lang}</small>
		</dd>
	</dl>

	<dl class="marginTop">
		<dt><label for="categoryID">{lang}fireball.acp.file.categoryIDs{/lang}</label></dt>
		<dd>
			<select id="categoryIDs" name="categoryIDs" multiple="multiple" size="10">
				{foreach from=$categoryList item=node}
					<option value="{@$node->categoryID}">{@"&nbsp;&nbsp;&nbsp;&nbsp;"|str_repeat:$categoryList->getDepth()}{$node->getTitle()}</option>
				{/foreach}
			</select>
		</dd>
	</dl>

	<div class="formSubmit">
		<button id="fileUploadSubmitButton" class="buttonPrimary" type="submit" disabled="disabled">{lang}wcf.global.button.submit{/lang}</button>
	</div>
</div>
