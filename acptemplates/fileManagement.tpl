{include file='header' pageTitle='cms.acp.file.management'}

<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function () {
		WCF.Language.addObject({
			'cms.acp.file.add': '{lang}cms.acp.file.add{/lang}',
			'cms.acp.file.details': '{lang}cms.acp.file.details{/lang}',
			'cms.acp.folder.add': '{lang}cms.acp.folder.add{/lang}',
			'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
		});

		new WCF.Action.Delete('cms\\data\\file\\FileAction', '.jsFileRow');
		new WCF.Action.Delete('cms\\data\\folder\\FolderAction', '.jsFolderRow');
		new CMS.ACP.File.Upload({$folderID}, true);

		$('#fileAdd').hide();
		$('#folderAdd').hide();

		$('#fileAddButton').click(function() {
			$('#fileAdd').wcfDialog({
				title: WCF.Language.get('cms.acp.file.add'),
				onClose: function() {
					location.reload();
				}
			});
		});

		$('#folderAddButton').click(function() {
			$('#folderAdd').wcfDialog({
				title: WCF.Language.get('cms.acp.folder.add')
			});
		});
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}cms.acp.file.management{/lang}</h1>
	<p>{lang}cms.acp.file.management.description{/lang}</p>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.add{/lang}</p>
{/if}

{if $errorField == 'file'}
	<p class="error">{lang}cms.acp.file.error.{$errorType}{/lang}</p>
{/if}

{if $errorField == 'folder'}
	<p class="error">{lang}cms.acp.folder.error.{$errorType}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a class="button small jsTooltip" id="folderAddButton" title="{lang}cms.acp.folder.add{/lang}"><span class="icon icon16 icon-folder-close"></span></a></li>
			<li><a class="button small jsTooltip" id="fileAddButton" title="{lang}cms.acp.file.add{/lang}"><span class="icon icon16 icon-upload"></span></a></li>
		</ul>
	</nav>
</div>

<div id="fileAdd">
	<div class="marginTop fileUpload" id="fileUpload">
		<ul></ul>
		<div id="fileUploadButton"></div>
		<small>{lang}cms.acp.file.add.description{/lang}</small>
	</div>
</div>

<form id="folderAdd" class="hidden" method="post" enctype="multipart/form-data" action="{link controller='FileManagement' application='cms'}action=folder{/link}">
	<fieldset>
		<dl{if $errorField == 'folder'} class="formError"{/if}>
			<dt><label for="folder">{lang}cms.acp.folder{/lang}</label></dt>
			<dd>
				<input type="text" name="folder" id="folder" class="long" value="{$foldername}" required="required"/>
				{if $errorField == 'folder'}
					<small class="innerError">
						{lang}cms.acp.folder.error.{$errorType}{/lang}
					</small>
				{/if}
			</dd>
		</dl>
	</fieldset>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{if $fileList|count || $folderList|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}cms.acp.file.list{/lang} <span class="badge badgeInverse">{#$fileList|count}</span></h2>
		</header>

		<table class="table">
			<thead>
				<th class="columnIcon">{lang}wcf.global.objectID{/lang}</th>
				<th class="columnTitle columnFile">{lang}cms.acp.file.title{/lang}</th>
				<th class="columnType">{lang}cms.acp.file.type{/lang}</th>
				<th class="downloads">{lang}cms.acp.file.downloads{/lang}</th>

				{event name='columnHeads'}
			</thead>
			<tbody>
				{if !$isFolder}
					{foreach from=$folderList item=folder}
						<tr class="jsFolderRow">
							<td class="columnIcon"><span class="icon icon-folder-close-alt icon16"></span> <span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$folder->folderID}" data-confirm-message="{lang}cms.acp.folder.delete.sure{/lang}"></span></td>
							<td class="columnTitle"><a href="{link controller='FileManagement' application='cms' object=$folder}{/link}">{$folder->getTitle()}</a></td>
							<td>{lang}cms.acp.folder{/lang}</td>
							<td>-</td>
						</tr>
					{/foreach}
				{else}
					<tr class="noFolders">
						<td class="columnIcon"><a href="{link controller='FileManagement' application='cms'}{/link}"><span class="icon icon16 icon-angle-left"></span></a></td>
						<td class="columnTitle"><a href="{link controller='FileManagement' application='cms'}{/link}">...</a></td>
						<td>{lang}cms.acp.folder.toRoot{/lang}</td>
						<td>-</td>
					</tr>
				{/if}

				{foreach from=$fileList item=file}
					<tr class="jsFileRow">
						<td class="columnIcon">
							{@$file->getIconTag()}
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$file->fileID}" data-confirm-message="{lang}cms.acp.file.delete.sure{/lang}"></span>
							<span>{$file->fileID}</span>
						</td>
						<td class="columnTitle columnFile"><a id="file{$file->fileID}" class="jsTooltip" title="{lang}cms.acp.file.details{/lang}">{$file->getTitle()}</a></td>
						<td class="columnType">{$file->type}</td>
						<td class="columnDownloads">{#$file->downloads}</td>
					</tr>

					<div class="details" id="details{$file->fileID}" style="display: none;">
						<fieldset>
							<legend>{@$file->getIconTag()} <a href="{$file->getURL()}">{$file->getTitle()}</a></fieldset>
						{if $file->type == 'image/png' || $file->type == 'image/jpeg' || $file->type == 'image/gif'}
							<figure class="framed">
								<img style="max-width: 300px" src="{$file->getURL()}" alt="" />
								<figcaption><small>{$file->size|filesize} | {$file->type}</small></figcaption>
							</figure>
						{else}
						<small>{$file->size|filesize} | {$file->type}</small>
						{/if}

						</fieldset>

						<fieldset>
							<legend>{lang}wcf.message.share{/lang}</legend>
							<input type="text" readonly="readonly" class="long" value="[cmsfile={$file->fileID}][/cmsfile]" />
							<br/><br/>
							<input type="text" readonly="readonly" class="long" value="{$file->getURL()}" />
						</fieldset>
					</div>

					<script data-relocate="true">
						//<![CDATA[
						$(function() {
							$('#details{$file->fileID}').hide();
							$('#file{$file->fileID}').click(function() {
								$('#details{$file->fileID}').wcfDialog({
									title: WCF.Language.get('cms.acp.file.details')
								});
							});
						});
						//]]>
					</script>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
