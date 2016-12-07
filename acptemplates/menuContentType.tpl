<dl>
	<dt><label for="contentData[type]">{lang}cms.acp.content.type.de.codequake.cms.content.type.menu.type{/lang}</label></dt>
	<dd>
		<select name="contentData[type]" id="contentData[type]">
			<option value="children">{lang}cms.acp.content.type.de.codequake.cms.content.type.menu.type.children{/lang}</option>
			<option value="all">{lang}cms.acp.content.type.de.codequake.cms.content.type.menu.type.all{/lang}</option>
		<select>
	</dd>
</dl>

<dl>
	<dt><label for="contentData[depth]">{lang}cms.acp.content.type.de.codequake.cms.content.type.menu.depth{/lang}</label></dt>
	<dd>
		<input type="number" name="contentData[depth]" id="contentData[depth]" value="{if $contentData['depth']|isset}{$contentData['depth']}{else}0{/if}"/>
		<small>{lang}cms.acp.content.type.de.codequake.cms.content.type.menu.depth.description{/lang}</small>
	</dd>
</dl>
