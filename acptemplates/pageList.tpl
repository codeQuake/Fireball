{include file='header' pageTitle='cms.acp.page.list'}

<script data-relocate="true">
    //<![CDATA[
    $(function() {
        new WCF.Action.Delete('cms\\data\\page\\PageAction', '.sortableNode', '> .sortableNodeLabel .jsDeleteButton');
        new WCF.Action.Toggle('cms\\data\\page\\PageAction', '.sortableNode', '> .sortableNodeLabel .jsToggleButton');
        new WCF.Sortable.List('pageList', 'cms\\data\\page\\PageAction', undefined, { protectRoot: true }, false, { });
            //]]>
</script>

<header class="boxHeadline">
    <h1>{lang}cms.acp.page.list{/lang}</h1>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='PageAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.page.add{/lang}</span></a></li>
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>
{hascontent}
<div class="container containerPadding marginTop shadow">
    <fieldset>
        <legend>{lang}cms.acp.page.pages{/lang}</legend>
        <div id="pageList" class="sortableListContainer">
            <ol class="sortableList simpleSortableList" data-object-id="0">
                {content}
                {foreach from=$objects item=page}
                    <li class="sortableNode" data-object-id="{@$page->pageID}">
                        <span class="sortableNodeLabel">
                            <a href="{link controller='PageEdit' application='cms' object=$page}{/link}">{lang}{$page->title}{/lang}</a>
									<span class="statusDisplay sortableButtonContainer">
										<a href="{link controller='PageEdit' application='cms' object=$page}{/link}" class="jsTooltip" title="{lang}wcf.global.button.edit{/lang}"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer"  title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$page->pageID}" data-confirm-message="{lang}cms.acp.page.delete.confirmMessage{/lang}"></span>
									</span>
                        </span>
						{if $page->countChildren() > 0}
						<ol class="sortableList simpleSortableList" data-object-id="{$page->pageID}">
						{foreach from=$page->getChildren() item=child}
							<li class="sortableNode" data-object-id="{@$child->pageID}">
								<span class="sortableNodeLabel">
									<a href="{link controller='PageEdit' application='cms' object=$child}{/link}">{lang}{$child->title}{/lang}</a>
											<span class="statusDisplay sortableButtonContainer">
												<a href="{link controller='PageEdit' application='cms' object=$child}{/link}" class="jsTooltip" title="{lang}wcf.global.button.edit{/lang}"><span class="icon icon16 icon-pencil"></span></a>
												<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer"  title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$child->pageID}" data-confirm-message="{lang}cms.acp.page.delete.confirmMessage{/lang}"></span>
											</span>
								</span>
							</li>
						{/foreach}
						</ol> 
						{/if}
                    </li>
                {/foreach}
                {/content}
            </ol>
            <div class="formSubmit">
					<button data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
			</div>
        </div>
    </fieldset>
</div>
{/hascontent}
<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='PageAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.page.add{/lang}</span></a></li>
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>
{include file='footer'}
