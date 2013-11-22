            <dl {if $errorField == 'sectionData'}class="formError"{/if}>
				<dt><label for="sectionData">{lang}cms.acp.content.section.data.boxID{/lang}</label></dt>
				<dd>
					<select id="sectionData" name="sectionData">
						{foreach from=$boxList item='item'}
						{if $item->boxType == 'content' && $content->position == 'body'}
						<option value="{$item->boxID}" {if $item->boxID == $boxID}selected="selected"{/if}>{lang}wcf.dashboard.box.{$item->boxName}{/lang}</option>
						{/if}
						{if $item->boxType == 'sidebar' && $content->position == 'sidebar'}
						<option value="{$item->boxID}" {if $item->boxID == $boxID}selected="selected"{/if}>{lang}wcf.dashboard.box.{$item->boxName}{/lang}</option>
						{/if}
						{/foreach}
					</select>
                    <p class="description">{lang}cms.acp.content.section.data.boxID.description{/lang}</p>
				</dd>
			</dl>