            <dl {if $errorField == 'sectionData'}class="formError"{/if}>
				<dt><label for="sectionData">{lang}cms.acp.content.section.data.fileID{/lang}</label></dt>
				<dd>
					<select id="sectionData" name="sectionData">
						<optgroup label="{lang}cms.acp.file.folderID.root{/lang}">
							{foreach from=$fileList item='item'}
							<option value="{$item->fileID}" {if $item->fileID == $fileID}selected="selected"{/if}>{$item->title|language}</option>
							{/foreach}
						</optgroup>
						{foreach from=$folderList item='folder'}
						<optgroup label="{$folder->getTitle()}">
							{foreach from=$folder->getFiles() item='item'}
							<option value="{$item->fileID}" {if $item->fileID == $fileID}selected="selected"{/if}>{$item->title|language}</option>
							{/foreach}
						</optgroup>
						{/foreach}
					</select>
                    <p class="description">{lang}cms.acp.content.section.data.fileID.description{/lang}</p>
				</dd>
			</dl>