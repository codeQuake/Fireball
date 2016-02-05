<dl>
	<dt><label for="contentData[fileID]">{lang}fireball.acp.content.type.de.codequake.cms.content.type.file.fileID{/lang}</label></dt>
	<dd>
		<div id="filePicker">
			<ul class="formAttachmentList clearfix"></ul>
			<span class="button small">{lang}fireball.acp.file.picker{/lang}</span>
		</div>
	</dd>
</dl>

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.Language.addObject({
			'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
		});

		new CMS.ACP.File.Preview();
		new CMS.ACP.File.Picker($('#filePicker > .button'), 'contentData[fileID]', {
			{if $file|isset}
				{@$file->fileID}: {
					fileID: {@$file->fileID},
					title: '{$file->getTitle()}',
					formattedFilesize: '{@$file->filesize|filesize}'
				}
			{/if}
		});
	});
	//]]>
</script>
