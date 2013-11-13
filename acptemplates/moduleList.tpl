{include file='header' pageTitle='cms.acp.module.list'}


<header class="boxHeadline">
    <h1>{lang}cms.acp.module.list{/lang}</h1>
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Action.Delete('cms\\data\\module\\ModuleAction', '.jsModuleRow');
		});
		//]]>
	</script>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='cms' controller="ModuleList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	<nav>
		<ul>
			<li><a href="{link controller='ModuleAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.module.add{/lang}</span></a></li>
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>


{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}cms.acp.module.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>
		
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnModuleID{if $sortField == 'moduleID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='ModuleList' application='cms'}pageNo={@$pageNo}&sortField=pageID&sortOrder={if $sortField == 'moduleID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle columnModule{if $sortField == 'moduleTitle'} active {@$sortOrder}{/if}"><a href="{link controller='StylemoduleList' application='cms'}pageNo={@$pageNo}&sortField=moduleTitle&sortOrder={if $sortField == 'moduleTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}cms.acp.module.title{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=module}
					<tr class="jsModuleRow">
						<td class="columnIcon">
							<a href="{link controller='ModuleEdit' id=$module->moduleID applicaton='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$module->moduleID}" data-confirm-message="{lang}cms.acp.module.delete.sure{/lang}"></span>
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$module->moduleID}</td>
						<td class="columnTitle columnModule"><a href="{link controller='ModuleEdit' id=$module->moduleID application='cms'}{/link}">{$module->getTitle()}</a></td>
						
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
				<li><a href="{link controller='ModuleAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.module.add{/lang}</span></a></li>
				
				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
