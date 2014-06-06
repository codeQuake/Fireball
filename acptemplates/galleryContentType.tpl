<dl class="images">
	<dt><label for="images">{lang}cms.acp.content.type.de.codequake.cms.content.type.gallery.images{/lang}</label></dt>
	<dd>
		<div id="imageSelect" class="marginTop">
			<span class="button small">{lang}cms.acp.content.type.de.codequake.cms.content.type.gallery.select{/lang}</span>
		</div>

		<input type="hidden" name="contentData[imageIDs]" value="{if $contentData['imageIDs']|isset}{$contentData['imageIDs']}{/if}" id="imageIDs" />
	</dd>
</dl>
<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
    //<![CDATA[
    $(function () {
		WCF.Language.addObject({
				'cms.acp.content.type.de.codequake.cms.content.type.gallery.select': '{lang}cms.acp.content.type.de.codequake.cms.content.type.gallery.select{/lang}'
		});
		new CMS.ACP.Content.Image.Gallery($('#imageSelect'), $('#imageIDs'));
		 });

    //]]>
</script>
