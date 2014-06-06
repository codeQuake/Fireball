<dl>
	<dt><label for="contentData[video]">{lang}cms.acp.content.type.de.codequake.cms.content.type.youtube.video{/lang}</label></dt>
	<dd>
		<input type="text" required="required" name="contentData[video]" id="contentData[video]" value="{if $contentData['video']|isset}{$contentData['video']}{/if}" class="long" />
	</dd>
</dl>

<dl>
	<dt><label for="contentData[size]">{lang}cms.acp.content.type.de.codequake.cms.content.type.youtube.size{/lang}</label></dt>
	<dd>
		<select name="contentData[size]" id="contentData[size]">
			<option value="1" {if $contentData['size']|isset && $contentData['size'] == 1}selected="selected"{/if}>cms.acp.content.type.de.codequake.cms.content.type.youtube.size1</option>
			<option value="2" {if $contentData['size']|isset && $contentData['size'] == 2}selected="selected"{/if}>cms.acp.content.type.de.codequake.cms.content.type.youtube.size2</option>
			<option value="3" {if $contentData['size']|isset && $contentData['size'] == 3}selected="selected"{/if}>cms.acp.content.type.de.codequake.cms.content.type.youtube.size3</option>
			<option value="4" {if $contentData['size']|isset && $contentData['size'] == 4}selected="selected"{/if}>cms.acp.content.type.de.codequake.cms.content.type.youtube.size4</option>
			<option value="5" {if $contentData['size']|isset && $contentData['size'] == 5}selected="selected"{/if}>cms.acp.content.type.de.codequake.cms.content.type.youtube.size5</option>
		</select>
	</dd>
</dl>
