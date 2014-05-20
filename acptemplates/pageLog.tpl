{include file='header' pageTitle='cms.acp.page.log'}

<nav class="breadcrumbs marginTop">
	<ul>
		<li title="{lang}cms.acp.page.overview{/lang}" itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
			<a href="{link controller='Overview' application='cms'}{/link}" itemprop="url">
				<span itemprop="title">{lang}cms.acp.page.overview{/lang}</span>
			</a>
			<span class="pointer">
				<span>»</span>
			</span>
		</li>
		<li title="{$page->getTitle()|language}" itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
			<a href="{link controller='PageEdit' object=$page application='cms'}{/link}" itemprop="url">
				<span itemprop="title">{$page->getTitle()|language}</span>
			</a>
			<span class="pointer">
				<span>»</span>
			</span>
		</li>
</nav>

<header class="boxHeadline">
    <h1>{lang}cms.acp.page.log{/lang}</h1>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='cms' controller='PageLog' object=$page link="pageNo=%d"}

	{hascontent}
		<nav>
			<ul>
				{content}
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

    {hascontent}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}cms.acp.page.log{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>

		<table class="table">
			<thead>
				<tr>
					<th class="columnID{if $sortField == 'logID'} active {@$sortOrder}{/if}"><a href="{link application='cms' controller='PageLog' object=$page}pageNo={@$pageNo}&sortField=logID&sortOrder={if $sortField == 'logID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnText">{lang}cms.acp.page.log.action{/lang}</th>
					<th class="columnText{if $sortField == 'username'} active {@$sortOrder}{/if}"><a href="{link application='cms' controller='PageLog' object=$page}pageNo={@$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.user.username{/lang}</a></th>
					<th class="columnDate{if $sortField == 'time'} active {@$sortOrder}{/if}"><a href="{link application='page' controller='PageLog' object=$page}pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}cms.acp.page.log.time{/lang}</a></th>

					{event name='columnHeads'}
				</tr>
			</thead>
			<tbody>
				{content}
					{foreach from=$objects item=entry}
						<tr>
							<td class="columnID">{#$entry->logID}</td>
							<td class="columnText">{@$entry}</td>
							<td class="columnText"><a href="{link controller='User' id=$entry->userID title=$entry->username}{/link}" class="userLink" data-user-id="{@$entry->userID}">{$entry->username}</a></td>
							<td class="columnDate">{@$entry->time|time}</td>

							{event name='columns'}
						</tr>
					{/foreach}
				{/content}
			</tbody>
		</table>
	</div>
{hascontentelse}
<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/hascontent}



{include file='footer'}
