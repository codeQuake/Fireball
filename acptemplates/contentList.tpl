{include file='header' pageTitle='cms.acp.content.list'}

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
	</ul>
</nav>

<header class="boxHeadline">
    <h1>{lang}cms.acp.content.list{/lang}</h1>

	<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js"></script>
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			WCF.TabMenu.init();
			new WCF.Action.Delete('cms\\data\\content\\ContentAction', '.jsContentRow');
			new WCF.Sortable.List('contentListSidebar', 'cms\\data\\content\\ContentAction');
			new WCF.Sortable.List('contentListBody', 'cms\\data\\content\\ContentAction');
			new CMS.ACP.Page.AddContent();
			WCF.Language.addObject({
				{foreach from=$objectTypeList item=type}
					'cms.acp.content.type.{$type->objectType}': '{lang}cms.acp.content.type.{$type->objectType}{/lang}',
				{/foreach}
				'cms.acp.content.type.content': '{lang}cms.acp.content.type.content{/lang}'
			});
		});
		//]]>
	</script>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Overview' application='cms'}{/link}" class="button"><span class="icon icon16 icon-gears"></span> <span>{lang}cms.acp.page.overview{/lang}</span></a></li>


			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>
<div class="tabMenuContainer">
<nav class="tabMenu">
	<ul>
		<li><a href="{@$__wcf->getAnchor('body')}">{lang}cms.acp.content.position.positon.body{/lang}</a></li>
		<li><a href="{@$__wcf->getAnchor('sidebar')}">{lang}cms.acp.content.position.positon.sidebar{/lang}</a></li>
		{event name='tabMenuTabs'}
	</ul>
</nav>
<div id="body" class="tabMenuContent container containerPadding">
{hascontent}
	<section id="contentListBody" class="sortableListContainer">
		<ol class="contentListBody sortableList" data-object-id="0">
			{content}
				{assign var=oldDepth value=0}
				{foreach from=$contentListBody item=content}
					{section name=i loop=$oldDepth-$contentListBody->getDepth()}</ol></li>{/section}
					<li class="content jsContentRow sortableNode" data-object-id="{$content->contentID}">
						<span class="sortableNodeLabel">
							<span class="title">
								<span class="pointer collapsibleButton icon icon16 {$content->getIcon()}"></span>
								<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}position=body{/link}">{@$content->getTitle()|language}</a> - <small>{lang}cms.acp.content.type.{$content->getTypeName()}{/lang}</small>
							</span>
							<span class="statusDisplay buttons">
								<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
								<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$content->contentID}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span>
								<span class="icon icon16 icon-plus jsContentAddButton jsTooltip pointer" title="{lang}cms.acp.page.content.add{/lang}" data-object-id="{@$content->pageID}" data-position="body"></span>

								{event name='itemButtons'}
							</span>
						</span>
						<ol class="contentListBody sortableList" data-object-id="{@$content->contentID}">
						{if !$contentListBody->current()->hasChildren()}
							</ol></li>
						{/if}
						{assign var=oldDepth value=$contentListBody->getDepth()}
				{/foreach}
				{section name=i loop=$oldDepth}</ol></li>{/section}
			{/content}
		</ol>
	</section>

	<div class="formSubmit">
			<button class="button buttonPrimary" data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
	</div>
{hascontentelse}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/hascontent}
</div>

<div id="sidebar" class="tabMenuContent container containerPadding">
{hascontent}
	<section id="contentListSidebar" class="sortableListContainer">
		<ol class="contentListSidebar sortableList" data-object-id="0">
			{content}
				{assign var=oldDepth value=0}
				{foreach from=$contentListSidebar item=content}
					{section name=i loop=$oldDepth-$contentListSidebar->getDepth()}</ol></li>{/section}
					<li class="content jsContentRow sortableNode" data-object-id="{$content->contentID}">
						<span class="sortableNodeLabel">
							<span class="title">
								<span class="pointer collapsibleButton icon icon16 {$content->getIcon()}"></span>
								<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}position=sidebar{/link}">{@$content->getTitle()|language}</a> - <small>{lang}cms.acp.content.type.{$content->getTypeName()}{/lang}</small>
							</span>
							<span class="statusDisplay buttons">
								<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
								<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$content->contentID}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span>
								<span class="icon icon16 icon-plus jsContentAddButton jsTooltip pointer" title="{lang}cms.acp.page.content.add{/lang}" data-object-id="{@$content->pageID}" data-position="sidebar"></span>

								{event name='itemButtons'}
							</span>
						</span>
						<ol class="contentListSidebar sortableList" data-object-id="{@$content->contentID}">
						{if !$contentListSidebar->current()->hasChildren()}
							</ol></li>
						{/if}
						{assign var=oldDepth value=$contentListSidebar->getDepth()}
				{/foreach}
				{section name=i loop=$oldDepth}</ol></li>{/section}
			{/content}
		</ol>
	</section>

	<div class="formSubmit">
			<button class="button buttonPrimary" data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
	</div>
{hascontentelse}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/hascontent}
</div>
{include file='footer'}
