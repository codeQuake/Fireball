<dl class="images">
	<dt><label for="images">{lang}cms.acp.content.type.de.codequake.cms.content.type.gallery.images{/lang}</label></dt>
	<dd>
		<div id="filePicker">
			<ul class="formAttachmentList clearfix"></ul>
			<span class="button small">{lang}cms.acp.file.picker{/lang}</span>
		</div>
	</dd>
</dl>

<script data-relocate="true">
	require(['Language'], function(Language) {
		Language.addObject({
			'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
		});

		new Fireball.ACP.File.Preview();
		new Fireball.ACP.File.Picker($('#filePicker > .button'), 'contentData[imageIDs]', {
		{if !$contentData['imageIDs']|empty}
			{assign var='imageList' value=$objectType->getProcessor()->getImageList($contentData['imageIDs'])}
			{implode from=$imageList item='image'}
				{@$image->fileID}: {
					fileID: {@$image->fileID},
					title: '{$image->getTitle()}',
						formattedFilesize: '{@$image->filesize|filesize}'
				}
			{/implode}
		{/if}
		}, { multiple: true, fileType: 'image' });
	});
</script>
