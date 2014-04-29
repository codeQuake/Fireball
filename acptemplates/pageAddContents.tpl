{if $pageID|isset}
<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new CMS.ACP.Sortable.List('bodyList', 'cms\\data\\content\\ContentAction');
			new CMS.ACP.Sortable.List('sidebarList', 'cms\\data\\content\\ContentAction');
			new WCF.Action.Delete('cms\\data\\content\\ContentAction', '.jsContentRow');
		});
		//]]>
</script>

<article class="page">
	<div>
		<section id="content" class="content">
			<fieldset class="sortableListContainer" id="bodyList">
				<legend>{lang}cms.acp.content.position.body{/lang}</legend>
				{if $page->getContentList('sidebar')|count}
				<ol class="bodyList sortableList" data-object-id="0">
					{foreach from=$page->getContentList() item=content}
						<li class="sortableNode sortableNoNesting jsContentRow" data-object-id="{$content->contentID}">
							<span class="sortableNodeLabel">
								<span class="icon icon16 icon-file-alt"></span>
								<span class="title"><a href="{link application='cms' controller='ContentEdit' id=$content->contentID}{/link}">{$content->getTitle()|language}</a></span>
								<span class="statusDisplay sortableButtonContainer">
									<a href="{link controller='ContentEdit' id=$content->contentID application='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
									<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$content->contentID}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span>
									<a href="{link controller='ContentSectionAdd' id=$content->contentID application='cms'}{/link}" title="{lang}cms.acp.content.content.section.add{/lang}" class="jsTooltip"><span class="icon icon16 icon-plus-sign"></span></a>
									<a href="{link controller='ContentSectionList' id=$content->contentID application='cms'}{/link}" title="{lang}cms.acp.content.content.section.list{/lang}" class="jsTooltip"><span class="icon icon16 icon-list-alt"></span></a>
								</span>
							</span>
						</li>
					{/foreach}
				</ol>
				{else}
					<p class="info">{lang}wcf.global.noItems{/lang}</p>
				{/if}
			</fieldset>
		</section>
		<aside class="side">
			<fieldset class="sortableListContainer" id="sidebarList">
				<legend>{lang}cms.acp.content.position.sidebar{/lang}</legend>
				{if $page->getContentList('sidebar')|count}
				<ol class="sidebarList sortableList" data-object-id="0">
					{foreach from=$page->getContentList('sidebar') item=content}
						<li class="sortableNode sortableNoNesting jsContentRow" data-object-id="{$content->contentID}">
							<span class="sortableNodeLabel"><span class="icon icon16 icon-file-alt"></span>
								<span class="title"><a href="{link application='cms' controller='ContentEdit' id=$content->contentID}{/link}">{$content->getTitle()|language}</a></span>
								<span class="statusDisplay sortableButtonContainer">
									<a href="{link controller='ContentEdit' id=$content->contentID application='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
									<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$content->contentID}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span>
									<a href="{link controller='ContentSectionAdd' id=$content->contentID application='cms'}{/link}" title="{lang}cms.acp.content.content.section.add{/lang}" class="jsTooltip"><span class="icon icon16 icon-plus-sign"></span></a>
									<a href="{link controller='ContentSectionList' id=$content->contentID application='cms'}{/link}" title="{lang}cms.acp.content.content.section.list{/lang}" class="jsTooltip"><span class="icon icon16 icon-list-alt"></span></a>
								</span>
							</span>
						</li>
					{/foreach}
				</ol>
				{else}
					<p class="info">{lang}wcf.global.noItems{/lang}</p>
				{/if}
			</fieldset>
		</aside>
	</div>
</article>
<div class="formSubmit">
	<a href="{link application='cms' controller='ContentAdd' id=$page->pageID}{/link}" class="button">{lang}cms.acp.content.add{/lang}</a>
	{if $page->getContentList('sidebar')|count || $page->getContentList()|count}
	<button type="button" class="button buttonPrimary" id="buttonSort" data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
	{/if}
</div>
{else}
	<p class="info">{lang}cms.acp.page.addContents.afterSaving{/lang}</p>
{/if}
