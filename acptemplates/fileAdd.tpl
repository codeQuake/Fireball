{capture assign='pageTitle'}{lang}cms.acp.file.management{/lang}{/capture}
{include file='header'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.file.management{/lang}</h1>
</header>
<script data-relocate="true">
    //<![CDATA[
    $(function () {
        new WCF.Action.Delete('cms\\data\\file\\FileAction', '.jsFileRow'); 
		new WCF.Action.Delete('cms\\data\\folder\\FolderAction', '.jsFolderRow');
    });
    //]]>
</script>

{include file='formError'}

{if $success|isset}
<p class="success">{lang}wcf.global.success.add{/lang}</p>
{/if}
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.Language.addObject({
				'cms.acp.file.add': '{lang}cms.acp.file.add{/lang}',
				'cms.acp.folder.add': '{lang}cms.acp.folder.add{/lang}'
				});
		$('#fileAdd').hide();
		$('#folderAdd').hide();
		$('#fileAddButton').click(function() {
			$('#fileAdd').wcfDialog({
				title: WCF.Language.get('cms.acp.file.add')
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
<div class="contentNavigation">
	<nav>
		<ul>
			<li><a class="button small jsTooltip" id="folderAddButton" title="{lang}cms.acp.folder.add{/lang}"><span class="icon icon16 icon-folder-close"></span></a></li>
			<li><a class="button small jsTooltip" id="fileAddButton" title="{lang}cms.acp.file.add{/lang}"><span class="icon icon16 icon-upload"></span></a></li>
		</ul>
	</nav>
</div>
<form id="fileAdd" class="hidden" method="post" enctype="multipart/form-data" action="{link controller='FileManagement' application='cms'}action=file{/link}">
    <div class="container containerPadding marginTop">
        <fieldset>
            <legend>{lang}cms.acp.file.file{/lang}</legend>
            <dl{if $errorField == 'file'} class="formError"{/if}>
                <dt><label for="file">{lang}cms.acp.file.file{/lang}</label></dt>
                <dd>
                    <input type="file" name="file" id="file"  required="required"/>
                    {if $errorField == 'file'}
                        <small class="innerError">
                              {lang}cms.acp.file.error.{$errorType}{/lang}
                        </small>
                    {/if}
                </dd>
            </dl>
			<dl {if $errorField == 'folderID'}class="formError"{/if}>
						<dt><label for="folderID">{lang}cms.acp.file.folderID{/lang}</label></dt>
						<dd>
							<select id="folderID" name="folderID">
								<option value="0" {if folderID == 0} selected="selected"{/if}>{lang}cms.acp.file.folderID.root{/lang}</option>
								{foreach from=$folderList item='item'}
								<option value="{$item->folderID}" {if $item->folderID == $folderID}selected="selected"{/if}>{$item->getTitle()|language}</option>
								{/foreach}
							</select>
						</dd>
			</dl>
        </fieldset>
    </div>

    <div class="formSubmit">
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		 {@SECURITY_TOKEN_INPUT_TAG}
    </div>
</form>

<form id="folderAdd" class="hidden" method="post" enctype="multipart/form-data" action="{link controller='FileManagement' application='cms'}action=folder{/link}">
    <div class="container containerPadding marginTop">
        <fieldset>
            <legend>{lang}cms.acp.folder{/lang}</legend>
            <dl{if $errorField == 'folder'} class="formError"{/if}>
                <dt><label for="folder">{lang}cms.acp.folder{/lang}</label></dt>
                <dd>
                    <input type="text" name="folder" id="folder" value="{$foldername}" required="required"/>
                    {if $errorField == 'folder'}
                        <small class="innerError">
                              {lang}cms.acp.folder.error.{$errorType}{/lang}
                        </small>
                    {/if}
                </dd>
            </dl>
        </fieldset>
    </div>

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
					<td class="columnTitle"><a href="{link controller='FileManagement' application='cms' object=$folder}{/link}">{$folder->getTitle()|language}</a></td>
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
                        </td> 
                        <td class="columnTitle columnFile"><a id="file{$file->fileID}">{$file->title|language}</a></td>
                        <td class="columnType">{$file->type}</td>
                        <td class="columnDownloads">{#$file->downloads}</td>
                    </tr>
					<div class="details" id="details{$file->fileID}" style="display: none;">
						<div class="boxSubHeadline">
							<h2>{@$file->getIconTag()} <a href="{$__wcf->getPath('cms')}files/{$file->filename}">{$file->title|language}</a></h2>
						</div>
						{if $file->type == 'image/png' || $file->type == 'image/jpeg' || $file->type == 'image/gif'}
							<div><img style="max-width: 300px" src="{$__wcf->getPath('cms')}files/{$file->filename}" alt="" /></div>
						{/if}
						<span>{$file->size|filesize} | {$file->type}</span>
					</div>
					<script data-relocate="true">
						//<![CDATA[
						$(function() {
							WCF.Language.addObject({
									'cms.acp.file.details': '{lang}cms.acp.file.details{/lang}'
									});
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
<p class="info">{lang}cms.acp.files.noItems{/lang}</p>
{/if}

{include file='footer'}