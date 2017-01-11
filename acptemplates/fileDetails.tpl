{include file='aclPermissionJavaScript' containerID='filePermissionsContainer' categoryName='user.*' objectTypeID=$fileACLObjectTypeID objectID=$file->fileID}

<div id="fileDetailDialog" data-object-id="{#$file->fileID}">
	<fieldset>
		<legend>{@$file->getIconTag()} <a href="{$file->getLink()}">{$file->getTitle()}</a></legend>

		{if $file->isImage()}
			<figure class="framed">
				<img style="max-width: 300px" src="{$file->getThumbnailLink()}" alt="" />
				<figcaption><small>{$file->filesize|filesize} | {$file->fileType}</small></figcaption>
			</figure>
		{else}
			<small>{$file->filesize|filesize} | {$file->fileType}</small>
		{/if}
	</fieldset>

	<fieldset>
		<legend>{lang}wcf.message.share{/lang}</legend>

		<input type="text" readonly="readonly" class="long" value="[cmsfile={@$file->fileID}][/cmsfile]" />
		<input type="text" readonly="readonly" class="long" value="{$file->getLink()}" style="margin-top: 9px" />
	</fieldset>

	<fieldset>
		<legend>{lang}cms.acp.file.userPermissions{/lang}</legend>

		<dl id="filePermissionsContainer">
			<dt>{lang}wcf.acl.permissions{/lang}</dt>
			<dd></dd>
		</dl>

		{event name='permissionFields'}
	</fieldset>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</div>
