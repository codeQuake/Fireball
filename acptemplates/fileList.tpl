{include file='header' pageTitle='cms.acp.file.list'}

<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function () {
		WCF.Language.addObject({
			'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
		});

		var actionObjects = { };
		actionObjects['de.codequake.cms.file'] = { };
		actionObjects['de.codequake.cms.file']['delete'] = new WCF.Action.Delete('cms\\data\\file\\FileAction', '.jsFileRow');

		WCF.Clipboard.init('cms\\acp\\page\\FileListPage', {@$hasMarkedItems}, actionObjects);

		var options = { };
		{if $pages > 1}
			options.refreshPage = true;
			{if $pages == $pageNo}
				options.updatePageNumber = -1;
			{/if}
		{else}
			options.emptyMessage = '{lang}wcf.global.noItems{/lang}';
		{/if}

		new WCF.Table.EmptyTableHandler($('#fileListTableContainer'), 'jsFileRow', options);

		new CMS.ACP.File.Details();
		CMS.ACP.File.Upload.init(function() {
			window.location.reload();
		});
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}cms.acp.file.list{/lang}</h1>
	<p>{lang}cms.acp.file.list.description{/lang}</p>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='cms' controller="FileList" link="id=$categoryID&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}

	<nav>
		<ul>
			<li class="dropdown">
				<a class="button dropdownToggle"><span class="icon icon16 icon-sort"></span> <span>{lang}wcf.category.button.choose{/lang}</span></a>
				<div class="dropdownMenu">
					<ul class="scrollableDropdownMenu">
						{foreach from=$categoryList item=node}
							<li{if $node->categoryID == $categoryID} class="active"{/if}><a href="{link application='cms' controller='FileList' id=$node->categoryID}{/link}">{@"&nbsp;&nbsp;&nbsp;&nbsp;"|str_repeat:$categoryList->getDepth()}{$node->getTitle()}</a></li>
						{/foreach}
					</ul>
				</div>
			</li>
			<li><a href="{link application='cms' controller='FileCategoryAdd'}{/link}" class="button"><span class="icon icon16 icon-folder-close"></span> <span>{lang}wcf.category.add{/lang}</span></a></li>
			<li><a class="button jsFileUploadButton"><span class="icon icon16 icon-upload"></span> <span>{lang}cms.acp.file.add{/lang}</span></a></li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $objects|count}
	<div id="fileListTableContainer" class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{$category->getTitle()} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>

		<table class="table jsClipboardContainer" data-type="de.codequake.cms.file">
			<thead>
				<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
				<th class="columnID columnFileID{if $sortField == 'fileID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link application='cms' controller='FileList' id=$categoryID}pageNo={@$pageNo}&sortField=fileID&sortOrder={if $sortField == 'fileID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
				<th class="columnTitle columnFilename{if $sortField == 'title'} active {@$sortOrder}{/if}" colspan="2"><a href="{link application='cms' controller='FileList' id=$categoryID}pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.title{/lang}</a></th>
				<th class="columnType">{lang}cms.acp.file.fileType{/lang}</th>
				<th class="columnDate columnUploadTime{if $sortField == 'uploadTime'} active {@$sortOrder}{/if}"><a href="{link application='cms' controller='FileList' id=$categoryID}pageNo={@$pageNo}&sortField=uploadTime&sortOrder={if $sortField == 'uploadTime' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}cms.acp.file.uploadTime{/lang}</a></th>
				<th class="columnDigits columnFilesize{if $sortField == 'filesize'} active {@$sortOrder}{/if}"><a href="{link application='cms' controller='FileList' id=$categoryID}pageNo={@$pageNo}&sortField=filesize&sortOrder={if $sortField == 'filesize' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}cms.acp.file.filesize{/lang}</a></th>
				<th class="columnDigits columnDownloads{if $sortField == 'downloads'} active {@$sortOrder}{/if}"><a href="{link application='cms' controller='FileList' id=$categoryID}pageNo={@$pageNo}&sortField=downloads&sortOrder={if $sortField == 'downloads' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}cms.acp.file.downloads{/lang}</a></th>

				{event name='columnHeads'}
			</thead>

			<tbody>
				{foreach from=$objects item=file}
					<tr class="jsClipboardObject jsFileRow">
						<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$file->fileID}" /></td>
						<td class="columnIcon">
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$file->fileID}" data-confirm-message="{lang}cms.acp.file.delete.sure{/lang}"></span>
						</td>
						<td class="columnID columnFileID">{@$file->fileID}</td>
						<td class="columnIcon">{@$file->getIconTag()}</td>
						<td class="columnTitle columnFile"><a class="jsFileDetails" data-file-id="{@$file->fileID}">{$file->getTitle()}</a></td>
						<td class="columnType">{$file->fileType}</td>
						<td class="columnDate columnUploadTime">{@$file->uploadTime|time}</td>
						<td class="columnDigits columnFilesize">{@$file->filesize|filesize}</td>
						<td class="columnDigits columnDownloads">{#$file->downloads}</td>

						{event name='columnRows'}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>

	<div class="contentNavigation">
		{@$pagesLinks}

		<nav>
			<ul>
				<li><a href="{link application='cms' controller='FileCategoryAdd'}{/link}" class="button"><span class="icon icon16 icon-folder-close"></span> <span>{lang}wcf.category.add{/lang}</span></a></li>
				<li><a class="button jsFileUploadButton"><span class="icon icon16 icon-upload"></span> <span>{lang}cms.acp.file.add{/lang}</span></a></li>

				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>

		<nav class="jsClipboardEditor" data-types="[ 'de.codequake.cms.file' ]"></nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
