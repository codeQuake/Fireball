            <dl {if $errorField == 'sectionData'}class="formError"{/if}>
				<dt><label for="sectionData">{lang}cms.acp.content.section.data.imageID{/lang}</label></dt>
				<dd>
					<select id="sectionData" name="sectionData[]" multiple="multiple" size="10" >
						{foreach from=$fileList item='item'}
						<option value="{$item->fileID}" {if $item->fileID|in_array:$fileIDs}selected="selected"{/if}>{$item->title|language}</option>
						{/foreach}
					</select>
                    <p class="description">{lang}cms.acp.content.section.data.imageID.description{/lang}</p>
				</dd>
			</dl>
			<dl>
				<dt><label for="resizeable">{lang}cms.acp.content.section.data.resizable{/lang}</label></dt>
				<dd><input type="checkbox" name="resizable" id="resizable" value="1" {if $resizable == 1}checked="checked"{/if} /></dd>
			</dl>