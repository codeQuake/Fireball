<dl class="image">
	<dt><label for="image">{lang}cms.acp.content.type.de.codequake.cms.content.type.image{/lang}</label></dt>
	<dd>
		<div id="filePicker">
			<ul class="formAttachmentList clearfix"></ul>
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
	require(['Language'], function(Language) {
		Language.addObject({
			'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
		});

		new Fireball.ACP.File.Preview();
		new Fireball.ACP.File.Picker($('#filePicker > .button'), 'contentData[imageID]', {
		{if !$contentData['imageID']|empty}
			{assign var=image value=$objectType->getProcessor()->getImage($contentData['imageID'])}
			{@$image->fileID}: {
				fileID: {@$image->fileID},
				title: '{$image->getTitle()}',
					formattedFilesize: '{@$image->filesize|filesize}'
			}
		{/if}
		}, { fileType: 'image' });

		{if $contentData['imageID']|isset && $contentData['imageID'] !== null}
			new Fireball.ACP.File.ImageRatio({$contentData['imageID']});
		{/if}
	});
</script>
