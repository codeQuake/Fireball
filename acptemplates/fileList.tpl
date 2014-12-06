{include file='header' pageTitle='cms.acp.file.management'}

<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function () {
		WCF.Language.addObject({
			'cms.acp.file.add': '{lang}cms.acp.file.add{/lang}',
			'cms.acp.file.details': '{lang}cms.acp.file.details{/lang}',
			'wcf.global.button.upload': '{lang}wcf.global.button.upload{/lang}'
		});

		new WCF.Action.Delete('cms\\data\\file\\FileAction', '.jsFileRow');
		new CMS.ACP.File.Upload({$categoryID}, true);

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

			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}cms.acp.file.list{/lang} <span class="badge badgeInverse">{#$objects|count}</span></h2>
		</header>

		<table class="table">
			<thead>
				<th class="columnID columnFileID" colspan="2">{lang}wcf.global.objectID{/lang}</th>
				<th class="columnTitle columnFile" colspan="2">{lang}cms.acp.file.title{/lang}</th>
				<th class="columnType">{lang}cms.acp.file.type{/lang}</th>
				<th class="downloads">{lang}cms.acp.file.downloads{/lang}</th>

				{event name='columnHeads'}
			</thead>

			<tbody>
				{foreach from=$objects item=file}
					<tr class="jsFileRow">
						<td class="columnIcon">
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$file->fileID}" data-confirm-message="{lang}cms.acp.file.delete.sure{/lang}"></span>
						</td>
						<td class="columnID columnFileID">{@$file->fileID}</td>
						<td class="columnIcon">{@$file->getIconTag()}</td>
						<td class="columnTitle columnFile"><a id="file{$file->fileID}" class="jsTooltip" title="{lang}cms.acp.file.details{/lang}">{$file->getTitle()}</a></td>
						<td class="columnType">{$file->type}</td>
						<td class="columnDownloads">{#$file->downloads}</td>
					</tr>

					<div class="details" id="details{$file->fileID}" style="display: none;">
						<fieldset>
							<legend>{@$file->getIconTag()} <a href="{$file->getLink()}">{$file->getTitle()}</a></legend>

							{if $file->type == 'image/png' || $file->type == 'image/jpeg' || $file->type == 'image/gif'}
								<figure class="framed">
									<img style="max-width: 300px" src="{$file->getLink()}" alt="" />
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
							<input type="text" readonly="readonly" class="long" value="{$file->getLink()}" />
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

<div id="fileAdd">
	<div class="marginTop fileUpload" id="fileUpload">
		<ul></ul>
		<div id="fileUploadButton"></div>
		<small>{lang}cms.acp.file.add.description{/lang}</small>
	</div>
</div>

{include file='footer'}
