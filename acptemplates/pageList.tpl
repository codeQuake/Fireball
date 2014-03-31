{include file='header' pageTitle='cms.acp.page.list'}

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
</nav>

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
	<nav>
		<ul>
			<li><a href="{link controller='Overview' application='cms'}{/link}" class="button"><span class="icon icon16 icon-gears"></span> <span>{lang}cms.acp.page.overview{/lang}</span></a></li>
			<li><a href="{link controller='PageAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.page.add{/lang}</span></a></li>
			
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>
{hascontent}
	<section id="pageList" class="container containerPadding marginTop sortableListContainer">
		<ol class="pageList sortableList" data-objectID="0">
			{content}
				{assign var=oldDepth value=0}
				{foreach from=$pageList item=page}
					{section name=i loop=$oldDepth-$pageList->getDepth()}</ol></li>{/section}
					<li class="page sortableNode" data-object-id="{$page->pageID}">
						<span class="sortableNodeLabel">
							<span class="title">
								<span class="pointer collapsibleButton icon icon16 icon-file-text-alt"></span>
								<a href="{link controller='PageEdit' application='cms' object=$page}{/link}">{@$page->getTitle()}</a> - <small>/{$page->getAlias()}/</small>
							</span>
							<span class="statusDisplay buttons">
							{if $page->isHome}
							<span class="icon icon16 icon-home jsTooltip" title="{lang}cms.acp.page.homePage{/lang}"></span>
							{/if}
								<a href="{link controller='PageEdit' application='cms' object=$page}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
								<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$page->pageID}" data-confirm-message="{lang}cms.acp.page.delete.sure{/lang}"></span>
							<a href="{link controller='ContentList' id=$page->pageID application='cms'}{/link}" title="{lang}cms.acp.page.content.list{/lang}" class="jsTooltip"><span class="icon icon16 icon-file"></span></a>
							{event name='itemButtons'}
							</span>
						</span>
						<ol class="pageList sortableList" data-object-id="{@$page->pageID}">
						{if !$pageList->current()->hasChildren()}
							</ol></li>
						{/if}
						{assign var=oldDepth value=$pageList->getDepth()}
				{/foreach}
				{section name=i loop=$oldDepth}</ol></li>{/section}
			{/content}
		</ol>
	</section>
{/hascontent}

{include file='footer'}
