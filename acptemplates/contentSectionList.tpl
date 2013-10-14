{include file='header' pageTitle='cms.acp.content.section.list'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.content.section.list{/lang}</h1>
    <script data-relocate="true">
        //<![CDATA[
        $(function () {
            new WCF.Action.Delete('cms\\data\\content\\section\\ContentSectionAction', '.jsContentSectionRow');
        });
        //]]>
	</script>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='cms' id=$contentID controller="ContentSectionList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	<nav>
		<ul>
			<li><a href="{link controller='ContentSectionAdd' application='cms' id=$contentID}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.section.add{/lang}</span></a></li>
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>


{if $objects|count}

    <div class="tabularBox tabularBoxTitle marginTop">
		    <header>
			    <h2>{lang}cms.acp.content.section.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
            </header>
        <table class="table">
			<thead>
				<tr>
					<th class="columnID columnSectionID{if $sortField == 'sectiontID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='ContentSectionList' id=$contentID application='cms'}pageNo={@$pageNo}&sortField=contentID&sortOrder={if $sortField == 'contentID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnTitle columnContentSection">{lang}cms.acp.content.section.content{/lang}</th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=section}
					<tr class="jsContentSectionRow">
						<td class="columnIcon">
                            <a href="{link controller='ContentSectionEdit' id=$section->sectionID applicaton='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
                            <span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$section->sectionID}" data-confirm-message="{lang}cms.acp.content.section.delete.sure{/lang}"></span>
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$section->sectionID}</td>
						<td class="columnTitle columnContent"><a href="{link controller='ContentSectionEdit' id=$section->sectionID application='cms'}{/link}">{@$section->getPreview()}</a></td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
        </div>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
