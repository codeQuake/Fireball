<dl>
	<dt><label for="contentData[fileID]">{lang}cms.acp.content.type.de.codequake.cms.content.type.file.fileID{/lang}</label></dt>
	<dd>
		<div id="filePicker">
			<ul class="formAttachmentList clearfix"></ul>
			<span class="button small">{lang}cms.acp.file.picker{/lang}</span>
		</div>
	</dd>
</dl>

<script data-relocate="true">
	$(function() {
		require(['Language'], function(Language) {
			Language.addObject({
				'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
			});
		});

		new Fireball.ACP.File.Preview();
		new Fireball.ACP.File.Picker($('#filePicker > .button'), 'contentData[fileID]', {
		{if !$contentData['fileID']|empty}
				{assign var=file value=$objectType->getProcessor()->getFile($contentData['fileID'])}
				{@$file->fileID}: {
					fileID: {@$file->fileID},
					title: '{$file->getTitle()}',
					formattedFilesize: '{@$file->filesize|filesize}'
				}
			{/if}
		});
	});
</script>
