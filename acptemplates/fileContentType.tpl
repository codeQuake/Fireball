<dl>
	<dt><label for="contentData[fileID]">{lang}cms.acp.content.type.de.codequake.cms.content.type.file.fileID{/lang}</label></dt>
	<dd>
		<select id="contentData[fileID]" name="contentData[fileID]">
			<optgroup label="{lang}cms.acp.content.type.de.codequake.cms.content.type.file.root{/lang}">
				{foreach from=$rootList item=file}
					<option value="{$file->fileID}" {if $contentData['fileID']|isset && $contentData['fileID'] == $file->fileID}selected="selected"{/if}>{$file->getTitle()}</option>
				{/foreach}
			</optgroup>
			{foreach from=$folderList item=folder}
				<optgroup label="{$folder->getTitle()}">
					{foreach from=$folder->getFiles() item=file}
						<option value="{$file->fileID}" {if $contentData['fileID']|isset && $contentData['fileID'] == $file->fileID}selected="selected"{/if}>{$file->getTitle()}</option>
					{/foreach}
				</optgroup>
			{/foreach}
		</select>
	</dd>
</dl>
