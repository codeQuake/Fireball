{include file='header' pageTitle='cms.acp.layout.list'}


<header class="boxHeadline">
	<h1>{lang}cms.acp.layout.list{/lang}</h1>
	<p>{lang}cms.acp.layout.list.description{/lang}</p>
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Action.Delete('cms\\data\\layout\\LayoutAction', '.jsLayoutRow');
		});
		//]]>
	</script>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='cms' controller="LayoutList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	<nav>
		<ul>
			<li><a href="{link controller='LayoutAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.layout.add{/lang}</span></a></li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>


{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}cms.acp.layout.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>

		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnLayoutID{if $sortField == 'layoutID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='LayoutList' application='cms'}pageNo={@$pageNo}&sortField=layoutID&sortOrder={if $sortField == 'layoutID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle columnLayout{if $sortField == 'title'} active {@$sortOrder}{/if}"><a href="{link controller='LayoutList' application='cms'}pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}cms.acp.layout.title{/lang}</a></th>

					{event name='columnHeads'}
				</tr>
			</thead>

			<tbody>
				{foreach from=$objects item=layout}
					<tr class="jsLayoutRow">
						<td class="columnIcon">
							<a href="{link controller='LayoutEdit' id=$layout->layoutID application='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$layout->layoutID}" data-confirm-message="{lang}cms.acp.layout.delete.sure{/lang}"></span>
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$layout->layoutID}</td>
						<td class="columnTitle columnLayout"><a href="{link controller='LayoutEdit' id=$layout->layoutID application='cms'}{/link}">{$layout->title}</a></td>

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
				<li><a href="{link controller='LayoutAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.layout.add{/lang}</span></a></li>

				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
