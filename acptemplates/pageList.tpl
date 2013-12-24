{include file='header' pageTitle='cms.acp.page.list'}


<header class="boxHeadline">
    <h1>{lang}cms.acp.page.list{/lang}</h1>
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Action.Delete('cms\\data\\page\\PageAction', '.jsPageRow');
		});
		//]]>
	</script>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='cms' controller="PageList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	<nav>
		<ul>
			<li><a href="{link controller='Overview' application='cms'}{/link}" class="button"><span class="icon icon16 icon-gears"></span> <span>{lang}cms.acp.page.overview{/lang}</span></a></li>
			<li><a href="{link controller='PageAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.page.add{/lang}</span></a></li>
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>


{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}cms.acp.page.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>
		
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnPageID{if $sortField == 'pageID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='PageList' application='cms'}pageNo={@$pageNo}&sortField=pageID&sortOrder={if $sortField == 'pageID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle columnPage{if $sortField == 'title'} active {@$sortOrder}{/if}"><a href="{link controller='PageList' application='cms'}pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}cms.acp.page.title{/lang}</a></th>
					<th class="columnDescription">{lang}cms.acp.page.description{/lang}</th>
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=page}
					<tr class="jsPageRow">
						<td class="columnIcon">
							<a href="{link controller='PageEdit' id=$page->pageID application='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$page->pageID}" data-confirm-message="{lang}cms.acp.page.delete.sure{/lang}"></span>
							<a href="{link controller='ContentList' id=$page->pageID application='cms'}{/link}" title="{lang}cms.acp.page.content.list{/lang}" class="jsTooltip"><span class="icon icon16 icon-file"></span></a>
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$page->pageID}</td>
						<td class="columnTitle columnPage"><a href="{link controller='PageEdit' id=$page->pageID application='cms'}{/link}">{$page->title|language}</a>
						{if $page->isHome}
						<aside class="statusDisplay">
								<ul class="statusIcons">
									<li><span class="icon icon16 icon-home jsTooltip" title="{lang}cms.acp.page.homePage{/lang}"></span></li>
								</ul>
							 {/if}
						 </aside>
						</td>
						<td>
							<span class="description">{$page->description|language}</span>
						</td>
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
				<li><a href="{link controller='Overview' application='cms'}{/link}" class="button"><span class="icon icon16 icon-gears"></span> <span>{lang}cms.acp.page.overview{/lang}</span></a></li>
				<li><a href="{link controller='PageAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.page.add{/lang}</span></a></li>
				
				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
