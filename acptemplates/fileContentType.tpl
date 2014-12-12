<dl>
	<dt><label for="contentData[fileID]">{lang}cms.acp.content.type.de.codequake.cms.content.type.file.fileID{/lang}</label></dt>
	<dd>
		<div id="filePicker">
			<span class="button small">{lang}cms.acp.file.picker{/lang}</span>
		</div>
	</dd>
</dl>

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.Language.addObject({
			'cms.acp.file.picker': '{lang}cms.acp.file.picker{/lang}'
		});

		new CMS.ACP.File.Picker($('#filePicker'), 'fileID', [{if $contentData['fileID']|isset}{@$contentData['fileID']}{/if}]);
	});
	//]]>
</script>
