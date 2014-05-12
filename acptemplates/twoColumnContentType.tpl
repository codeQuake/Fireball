<dl>
	<dt><label for="contentData[width]">{lang}cms.acp.content.type.de.codequake.cms.content.type.twocolumn.width{/lang}</label></dt>
	<dd>
		<select id="contentData[width]" name="contentData[width]" >
			<option value="5050" {if $contentData['width']|isset && $contentData['width'] == 5050}selected="selected" {/if}>50:50</option>
			<option value="6040" {if $contentData['width']|isset && $contentData['width'] == 6040}selected="selected" {/if}>60:40</option>
			<option value="4060" {if $contentData['width']|isset && $contentData['width'] == 4060}selected="selected" {/if}>40:60</option>
			<option value="7030" {if $contentData['width']|isset && $contentData['width'] == 7030}selected="selected" {/if}>70:30</option>
			<option value="3070" {if $contentData['width']|isset && $contentData['width'] == 3070}selected="selected" {/if}>30:70</option>
		</select>
	</dd>
</dl>
