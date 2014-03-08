{include file='header' pageTitle='cms.acp.feed.list'}


<header class="boxHeadline">
    <h1>{lang}cms.acp.feed.list{/lang}</h1>
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Action.Delete('cms\\data\\feed\\FeedAction', '.jsFeedRow');
		});
		//]]>
	</script>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='cms' controller="FeedList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	<nav>
		<ul>
			<li><a href="{link controller='FeedAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.feed.add{/lang}</span></a></li>
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>


{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}cms.acp.feed.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>
		
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnFeedID{if $sortField == 'feedID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='FeedList' application='cms'}pageNo={@$pageNo}&sortField=feedID&sortOrder={if $sortField == 'feedID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle columnFeed{if $sortField == 'title'} active {@$sortOrder}{/if}"><a href="{link controller='FeedList' application='cms'}pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}cms.acp.feed.title{/lang}</a></th>
					<th class="columnUrl">{lang}cms.acp.feed.url{/lang}</th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=feed}
					<tr class="jsFeedRow">
						<td class="columnIcon">
							<a href="{link controller='FeedEdit' id=$feed->feedID application='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$feed->feedID}" data-confirm-message="{lang}cms.acp.feed.delete.sure{/lang}"></span>
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$feed->feedID}</td>
						<td class="columnTitle columnLayout"><a href="{link controller='FeedEdit' id=$feed->feedID application='cms'}{/link}">{$feed->title}</a></td>
						<td class="columnUrl"><a href="{$feed->feedUrl}">{$feed->feedUrl}</a></td>
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
				<li><a href="{link controller='FeedAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.feed.add{/lang}</span></a></li>
				
				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
