<dl class="images">
	<dt><label for="images">{lang}cms.acp.content.type.de.codequake.cms.content.type.gallery.images{/lang}</label></dt>
	<dd>
		<div id="filePicker">
			<span class="button small">{lang}cms.acp.file.picker{/lang}</span>
		</div>
	</dd>
</dl>

<script data-relocate="true">
	//<![CDATA[
	$(function () {
		WCF.Language.addObject({
			'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
		});

		new CMS.ACP.File.Preview();
		new CMS.ACP.File.Picker($('#filePicker'), 'contentData[imageIDs]', [{if $contentData['imageIDs']|isset}{implode from=$contentData['imageIDs'] item='imageID'}{@$imageID}{/implode}{/if}], { multiple: true, fileType: 'image' });
	});
	//]]>
</script>
