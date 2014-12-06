{include file='header' pageTitle='cms.acp.file.list'}

<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function () {
		WCF.Language.addObject({
			'cms.acp.file.add': '{lang}cms.acp.file.add{/lang}',
			'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
		});

		var actionObjects = { };
		actionObjects['de.codequake.cms.file'] = { };
		actionObjects['de.codequake.cms.file']['delete'] = new WCF.Action.Delete('cms\\data\\file\\FileAction', '.jsFileRow');

		WCF.Clipboard.init('cms\\acp\\page\\FileListPage', {@$hasMarkedItems}, actionObjects);

		new CMS.ACP.File.Details();
		new CMS.ACP.File.Upload(true);

		$('#fileAdd').hide();

		$('#fileAddButton').click(function() {
			$('#fileAdd').wcfDialog({
				title: WCF.Language.get('cms.acp.file.add'),
				onClose: function() {
					location.reload();
				}
			});
		});
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}cms.acp.file.list{/lang}</h1>
	<p>{lang}cms.acp.file.list.description{/lang}</p>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='cms' controller="FileList" link="id=$categoryID&pageNo=%d"}

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
			<li><a class="button" id="fileAddButton"><span class="icon icon16 icon-upload"></span> <span>{lang}cms.acp.file.add{/lang}</span></a></li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{$category->getTitle()} <span class="badge badgeInverse">{#$objects|count}</span></h2>
		</header>

		<table class="table jsClipboardContainer" data-type="de.codequake.cms.file">
			<thead>
				<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
				<th class="columnID columnFileID" colspan="2">{lang}wcf.global.objectID{/lang}</th>
				<th class="columnTitle columnFile" colspan="2">{lang}cms.acp.file.title{/lang}</th>
				<th class="columnType">{lang}cms.acp.file.type{/lang}</th>
				<th class="downloads">{lang}cms.acp.file.downloads{/lang}</th>

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
						<td class="columnTitle columnFile"><a class="jsFileDetails jsTooltip" title="{lang}cms.acp.file.details{/lang}" data-file-id="{@$file->fileID}">{$file->getTitle()}</a></td>
						<td class="columnType">{$file->type}</td>
						<td class="columnDownloads">{#$file->downloads}</td>

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
				<li><a class="button" id="fileAddButton"><span class="icon icon16 icon-upload"></span> <span>{lang}cms.acp.file.add{/lang}</span></a></li>

				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>

		<nav class="jsClipboardEditor" data-types="[ 'de.codequake.cms.file' ]"></nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<div id="fileAdd">
	<div class="marginTop fileUpload" id="fileUpload">
		<dl>
			<dt>{lang}cms.acp.file.files{/lang}</dt>
			<dd>
				<ul></ul>
				<div id="fileUploadButton"></div>
				<small class="marginTopSmall">{lang}cms.acp.file.files.description{/lang}</small>
			</dd>
		</dl>

		<dl class="marginTop">
			<dt><label for="categoryID">{lang}cms.acp.file.categoryID{/lang}</label></dt>
			<dd>
				<select id="categoryID" name="categoryID">
					{foreach from=$categoryList item=node}
						<option value="{@$node->categoryID}"{if $node->categoryID == $categoryID} selected="selected"{/if}>{@"&nbsp;&nbsp;&nbsp;&nbsp;"|str_repeat:$categoryList->getDepth()}{$node->getTitle()}</option>
					{/foreach}
				</select>
			</dd>
		</dl>
	</div>
</div>

{include file='footer'}
