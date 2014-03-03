{include file='header' pageTitle='cms.acp.restore.list'}


<header class="boxHeadline">
    <h1>{lang}cms.acp.restore.list{/lang}</h1>
	<script data-relocate="true">
	    //<![CDATA[
	    $(function () {
	        new WCF.Action.Delete('cms\\data\\restore\\RestoreAction', '.jsRestoreRow');
	    });
	    //]]>
	</script>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a class="button jsTooltip" href="{link controller="CMSExport" application="cms"}{/link}" title="{lang}cms.acp.restore.create{/lang}"><span class="icon icon16 icon-asterisk"></span> <span>{lang}cms.acp.restore.create{/lang}<span></a></li>
		</ul>
	</nav>{pages print=true assign=pagesLinks application='cms' controller="RestoreList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
</div>


{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}cms.acp.restore.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>
		
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnRestoreID{if $sortField == 'restoreID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='RestoreList' application='cms'}pageNo={@$pageNo}&sortField=restoreID&sortOrder={if $sortField == 'restoreID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle columnRestore{if $sortField == 'time'} active {@$sortOrder}{/if}"><a href="{link controller='RestoreList' application='cms'}pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}cms.acp.restore.time{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=restore}
					<tr class="jsRestoreRow">
						<td class="columnIcon">
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$restore->restoreID}" data-confirm-message="{lang}cms.acp.restore.delete.sure{/lang}"></span>
							<span class="icon icon16 icon-ambulance jsRestoreButton jsTooltip pointer" title="{lang}cms.acp.restore.restore{/lang}" data-object-id="{@$restore->restoreID}"></span>
							<a title="{lang}cms.acp.restore.download{/lang}" class="jsTooltip pointer" href="{link controller="RestoreDownload" id=$restore->restoreID application="cms"}{/link}"><span class="icon icon16 icon-download"></span></a>
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$restore->restoreID}</td>
						<td class="columnTitle columnRestore">{@$restore->time|time}</td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
		
	</div>
	
	<div class="contentNavigation">
		{@$pagesLinks}
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
