{include file='header' pageTitle='cms.acp.content.section.list'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.content.section.list{/lang}</h1>
    <script data-relocate="true">
        //<![CDATA[
        $(function () {
            new WCF.Action.Delete('cms\\data\\content\\section\\ContentSectionAction', '.jsContentSectionRow');
			new WCF.Sortable.List('sectionList', 'cms\\data\\content\\section\\ContentSectionAction');
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
<div id="sectionList" class="container containerPadding sortableListContainer marginTop">
	<ol id="sectionContainer0" class="sortableList" data-object-id="0">
		{foreach from=$objects item=section}
		<li class="sortableNode" data-object-id="{$section->sectionID}">
			<span class="sortableNodeLabel">
				<a href="{link controller='ContentSectionEdit' id=$section->sectionID application='cms'}{/link}">{@$section->getPreview()}</a>
				<span class="statusDisplay sortableButtonContainer">
					<a href="{link controller='ContentSectionEdit' id=$section->sectionID applicaton='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
					<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$section->sectionID}" data-confirm-message="{lang}cms.acp.content.section.delete.sure{/lang}"></span>
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
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
