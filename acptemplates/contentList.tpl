{include file='header' pageTitle='cms.acp.content.list'}


<header class="boxHeadline">
    <h1>{lang}cms.acp.content.list{/lang}</h1>
    <script data-relocate="true">
        //<![CDATA[
        $(function () {
            new WCF.Action.Delete('cms\\data\\content\\ContentAction', '.jsContentNode');
			new WCF.Sortable.List('contentList', 'cms\\data\\content\\ContentAction');
        });
        //]]>
	</script>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='cms' id=$pageID controller="ContentList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	<nav>
		<ul>
			<li><a href="{link controller='ContentAdd' application='cms' id=$pageID}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></a></li>
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $objects|count}
    {if $pageID != 0}
		<div id="contentList" class="container containerPadding sortableListContainer marginTop">
			<ol id="sectionContainer0" class="sortableList" data-object-id="0">
				{foreach from=$objects item=content}
				<li class="sortableNode jsContentNode" data-object-id="{$content->contentID}">
					<span class="sortableNodeLabel">
						<a href="{link controller='ContentEdit' id=$content->contentID application='cms'}{/link}">{@$content->getTitle()|language}</a>
						<span class="statusDisplay sortableButtonContainer">
							<a href="{link controller='ContentEdit' id=$content->contentID applicaton='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$content->contentID}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span>
							<a href="{link controller='ContentSectionAdd' id=$content->contentID application='cms'}{/link}" title="{lang}cms.acp.content.content.section.add{/lang}" class="jsTooltip"><span class="icon icon16 icon-plus-sign"></span></a>
							<a href="{link controller='ContentSectionList' id=$content->contentID application='cms'}{/link}" title="{lang}cms.acp.content.content.section.list{/lang}" class="jsTooltip"><span class="icon icon16 icon-list-alt"></span></a>

						</span>
					</span>
				</li>
				{/foreach}
			</ol>
		</div>
		<div class="formSubmit">
			<button class="button buttonPrimary" data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
		</div>
    {else}
        <div class="tabularBox tabularBoxTitle marginTop">
		    <header>
			    <h2>{lang}cms.acp.content.page.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
            </header>
        <table class="table">
			<thead>
				<tr>
					<th class="columnID columnPageID{if $sortField == 'pageID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='ContentList' application='cms'}pageNo={@$pageNo}&sortField=pageID&sortOrder={if $sortField == 'pageID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle columnPage{if $sortField == 'title'} active {@$sortOrder}{/if}"><a href="{link controller='ContentList' application='cms'}pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}cms.acp.page.title{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=page}
					<tr class="jsPageRow">
						<td class="columnIcon">
							<a href="{link controller='ContentAdd' id=$page->pageID application='cms'}{/link}" title="{lang}cms.acp.page.content.add{/lang}" class="jsTooltip"><span class="icon icon16 icon-plus-sign"></span></a>
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$page->pageID}</td>
						<td class="columnTitle columnPage"><a href="{link controller='ContentList' id=$page->pageID application='cms'}{/link}">{$page->title|language}</a></td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
        </div>
    {/if}
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
