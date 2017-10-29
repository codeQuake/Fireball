<dl class="images">
	<dt></dt>
	<dd class="wide">
		<div id="filePicker" class="sortableListContainer">
			<ol id="filePickerList" class="sortableList"></ol>
			<span class="button small">{lang}cms.acp.file.picker{/lang}</span>
		</div>
	</dd>
</dl>

<script data-relocate="true">
	require(['Language', 'WoltLabSuite/Core/Ui/Sortable/List'], function(Language, UiSortableList) {
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
					formattedFilesize: '{@$image->filesize|filesize}',
					imageUrl: '{$image->getLink()}'
				}
			{/implode}
		{/if}
		}, { multiple: true, fileType: 'image'{if !$contentData[imageIDs][ordered]|empty}, sortOrder: [ {', '|implode:$contentData[imageIDs][ordered]} ]{/if} });

		new UiSortableList({
			containerId: 'filePicker',
			className: 'cms\\data\\file\\FileAction',
			isSimpleSorting: true
		});

	});
</script>
