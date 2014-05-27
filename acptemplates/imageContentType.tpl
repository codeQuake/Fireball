{$errorField}
<dl class="image">
	<dt><label for="image">{lang}cms.acp.content.type.de.codequake.cms.content.type.image{/lang}</label></dt>
	<dd>
		<ul>
			{if $contentData['imageID']|isset && $file|isset}
				{assign var=image value=$file->getByID($contentData['imageID'])}
				<li class="box32">
					<div class="framed">
						<img src="{$image->getURL()}" alt="{$image->title}" class="image" style="max-width: 32px; max-height: 32px;" />
					</div>
					<div>
						<p>{$image->title}</p>
					</div>
				</li>
			{/if}
		</ul>
		<div id="imageSelect" class="marginTop">
			<span class="button small">{lang}cms.acp.content.type.de.codequake.cms.content.type.image.select{/lang}</span>
		</div>

		<input type="hidden" name="contentData[imageID]" value="{if $contentData['imageID']|isset}{$contentData['imageID']}{else}0{/if}" id="imageID" />
	</dd>
</dl>
<dl>
	<dt><label for="text">{lang}cms.acp.content.type.de.codequake.cms.content.type.image.text{/lang}</label></dt>
	<dd>
		<input name="text" id="text" type="text" value="{$i18nPlainValues['text']}"  class="long" />
	</dd>
</dl>

{include file='multipleLanguageInputJavascript' elementIdentifier='text' forceSelection=false}

<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
    //<![CDATA[
    $(function () {
		WCF.Language.addObject({
				'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
		});
		new CMS.ACP.Content.Image($('#imageSelect'), $('#imageID'));
    });
    //]]>
</script>
