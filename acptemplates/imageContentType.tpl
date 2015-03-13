<dl class="image">
	<dt><label for="image">{lang}cms.acp.content.type.de.codequake.cms.content.type.image{/lang}</label></dt>
	<dd>
		<div id="filePicker">
			<span class="button small">{lang}cms.acp.file.picker{/lang}</span>
		</div>
	</dd>
</dl>

<dl>
	<dt><label for="text">{lang}cms.acp.content.type.de.codequake.cms.content.type.image.text{/lang}</label></dt>
	<dd>
		<input name="text" id="text" type="text" value="{$i18nPlainValues['text']}"  class="long" />

		{include file='multipleLanguageInputJavascript' elementIdentifier='text' forceSelection=false}
	</dd>
</dl>

<dl>
	<dt><label for="width">{lang}cms.acp.content.type.de.codequake.cms.content.type.image.width{/lang}</label></dt>
	<dd>
		<input name="contentData[width]" id="width" type="number" value="{if $contentData['width']|isset}{$contentData['width']}{else}0{/if}"  />
	</dd>
</dl>

<dl>
	<dt><label for="height">{lang}cms.acp.content.type.de.codequake.cms.content.type.image.height{/lang}</label></dt>
	<dd>
		<input name="contentData[height]" id="height" type="number" value="{if $contentData['height']|isset}{$contentData['height']}{else}0{/if}" />
	</dd>
</dl>

<dl>
	<dt><label for="contentData[link]">{lang}cms.acp.content.type.de.codequake.cms.content.type.image.hyperlink{/lang}</label></dt>
	<dd>
		<input name="contentData[link]" id="contentData[link]" type="text" value="{if $contentData['link']|isset}{$contentData['link']}{/if}"  class="long" />
	</dd>
</dl>

<script data-relocate="true">
	//<![CDATA[
	$(function () {
		WCF.Language.addObject({
			'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
		});

		new CMS.ACP.File.Preview();
		new CMS.ACP.File.Picker($('#filePicker'), 'contentData[imageID]', [{if $contentData['imageID']|isset}{@$contentData['imageID']}{/if}], { fileType: 'image' });

		{if $contentData['imageID']|isset && $contentData['imageID'] !== null}
			new CMS.ACP.Image.Ratio({$contentData['imageID']});
		{/if}
	});
	//]]>
</script>
