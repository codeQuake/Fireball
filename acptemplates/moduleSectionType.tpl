            <dl {if $errorField == 'sectionData'}class="formError"{/if}>
				<dt><label for="sectionData">{lang}cms.acp.content.section.data.moduleID{/lang}</label></dt>
				<dd>
					<select id="sectionData" name="sectionData">
						{foreach from=$moduleList item='item'}
						<option value="{$item->moduleID}" {if $item->moduleID == $moduleID}selected="selected"{/if}>{$item->getTitle()|language}</option>
						{/foreach}
					</select>
                    <p class="description">{lang}cms.acp.content.section.data.moduleID.description{/lang}</p>
				</dd>
			</dl>