<dl>
	<dt><label for="contentData[width]">{lang}cms.acp.content.type.de.codequake.cms.content.type.fourcolumns.width{/lang}</label></dt>
	<dd>
		<select id="contentData[width]" name="contentData[width]" >
			<option value="25252525" {if $contentData['width']|isset && $contentData['width'] == 25252525}selected="selected" {/if}>25%:25%:25%:25%</option>
			<option value="50201515" {if $contentData['width']|isset && $contentData['width'] == 50201515}selected="selected" {/if}>50%:20%:15%:15%</option>
		</select>
	</dd>
</dl>
