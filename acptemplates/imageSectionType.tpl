            <dl {if $errorField == 'sectionData'}class="formError"{/if}>
				<dt><label for="sectionData">{lang}cms.acp.content.section.data.imageID{/lang}</label></dt>
				<dd>
					<select id="sectionData" name="sectionData">
						{foreach from=$fileList item='item'}
						<option value="{$item->fileID}" {if $item->fileID == $fileID}selected="selected"{/if}>{$item->title|language}</option>
						{/foreach}
					</select>
                    <p class="description">{lang}cms.acp.content.section.data.imageID.description{/lang}</p>
				</dd>
			</dl>
			<dl>
				<dt><label for="subtitle">{lang}cms.acp.content.section.data.subtitle{/lang}</label></dt>
				<dd><input type="text" name="subtitle" class="long" id="subtitle" value="{$subtitle}" /></dd>
			</dl>
			<dl>
				<dt><label for="link">{lang}cms.acp.content.section.data.link{/lang}</label></dt>
				<dd><input type="text" name="link" class="long" id="link" value="{$link}" /></dd>
			</dl>
			<dl>
				<dt><label for="resizeable">{lang}cms.acp.content.section.data.resizable{/lang}</label></dt>
				<dd><input type="checkbox" name="resizable" id="resizable" value="1" {if $resizable == 1}checked="checked"{/if} /></dd>
			</dl>