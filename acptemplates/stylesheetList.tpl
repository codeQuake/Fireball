{include file='header' pageTitle='cms.acp.stylesheet.list'}

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		var actionObjects = { };
		actionObjects['de.codequake.cms.stylesheet'] = { };
		actionObjects['de.codequake.cms.stylesheet']['delete'] = new WCF.Action.Delete('cms\\data\\stylesheet\\StylesheetAction', '.jsStylesheetRow');

		WCF.Clipboard.init('cms\\acp\\page\\StylesheetListPage', {@$hasMarkedItems}, actionObjects);
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}cms.acp.stylesheet.list{/lang}</h1>
	<p>{lang}cms.acp.stylesheet.list.description{/lang}</p>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='cms' controller="StylesheetList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}

	<nav>
		<ul>
			<li><a href="{link controller='StylesheetAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.stylesheet.add{/lang}</span></a></li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>


{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}cms.acp.stylesheet.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>

		<table class="table jsClipboardContainer" data-type="de.codequake.cms.stylesheet">
			<thead>
				<tr>
					<th class="columnMark"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>
					<th class="columnID columnSheetID{if $sortField == 'sheetID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='StylesheetList' application='cms'}pageNo={@$pageNo}&sortField=sheetID&sortOrder={if $sortField == 'sheetID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle columnStylesheet{if $sortField == 'title'} active {@$sortOrder}{/if}"><a href="{link controller='StylesheetList' application='cms'}pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.title{/lang}</a></th>

					{event name='columnHeads'}
				</tr>
			</thead>

			<tbody>
				{foreach from=$objects item=sheet}
					<tr class="jsClipboardObject jsStylesheetRow">
						<td class="columnMark"><input type="checkbox" class="jsClipboardItem" data-object-id="{@$sheet->sheetID}" /></td>
						<td class="columnIcon">
							<a href="{link controller='StylesheetEdit' id=$sheet->sheetID application='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$sheet->sheetID}" data-confirm-message="{lang}cms.acp.stylesheet.delete.sure{/lang}"></span>
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$sheet->sheetID}</td>
						<td class="columnTitle columnStylesheet"><a href="{link controller='StylesheetEdit' id=$sheet->sheetID application='cms'}{/link}">{$sheet->title}</a></td>

						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>

	</div>

	<div class="contentNavigation">
		{@$pagesLinks}

		<nav>
			<ul>
				<li><a href="{link controller='StylesheetAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.stylesheet.add{/lang}</span></a></li>

				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>

		<nav class="jsClipboardEditor" data-types="[ 'de.codequake.cms.stylesheet' ]"></nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
