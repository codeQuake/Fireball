<dl>
	<dt><label for="contentData[width]">{lang}cms.acp.content.type.de.codequake.cms.content.type.twocolumn.width{/lang}</label></dt>
	<dd>
		<select id="contentData[width]" name="contentData[width]" >
			<option value="333333" {if $contentData['width']|isset && $contentData['width'] == 333333}selected="selected" {/if}>33%:33%:33%</option>
			<option value="502525" {if $contentData['width']|isset && $contentData['width'] == 502525}selected="selected" {/if}>50%:25%:25%</option>
			<option value="255025" {if $contentData['width']|isset && $contentData['width'] == 255025}selected="selected" {/if}>25%:50%:25%</option>
			<option value="252550" {if $contentData['width']|isset && $contentData['width'] == 252550}selected="selected" {/if}>25%:25%:50%</option>
		</select>
	</dd>
</dl>
