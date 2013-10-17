            <dl {if $errorField == 'sectionData'}class="formError"{/if}>
				<dt><label for="sectionData">{lang}cms.acp.content.section.data.fileID{/lang}</label></dt>
				<dd>
					<select id="sectionData" name="sectionData">
						{foreach from=$fileList item='item'}
						<option value="{$item->fileID}" {if $item->fileID == $fileID}selected="selected"{/if}>{$item->title|language}</option>
						{/foreach}
					</select>
				</dd>
			</dl>