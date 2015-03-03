{include file='header' pageTitle='cms.acp.content.list'}

<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP{if !ENABLE_DEBUG_MODE}.min{/if}.js"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.TabMenu.init();

		var deleteAction = new WCF.Action.NestedDelete('cms\\data\\content\\ContentAction', '.jsContentRow');
		var toggleAction = new WCF.Action.Toggle('cms\\data\\content\\ContentAction', '.jsContentRow', '> .sortableNodeLabel > .buttons > .jsToggleButton');

		var actionObjects = { };
		actionObjects['de.codequake.cms.content'] = { };
		actionObjects['de.codequake.cms.content']['disable'] = actionObjects['de.codequake.cms.content']['enable'] = toggleAction;
		actionObjects['de.codequake.cms.content']['delete'] = deleteAction;

		WCF.Clipboard.init('cms\\acp\\page\\ContentListPage', {@$hasMarkedItems}, actionObjects);

		new WCF.Sortable.List('contentListSidebar', 'cms\\data\\content\\ContentAction');
		new WCF.Sortable.List('contentListBody', 'cms\\data\\content\\ContentAction');

		new CMS.ACP.Page.AddContent();
		new CMS.ACP.Content.Revisions();

		new WCF.Action.SimpleProxy({
			action: 'copy',
			className: 'cms\\data\\content\\ContentAction',
			elements: $('.jsContentRow .jsCopyButton')
		}, {
			success: function() {
				window.location.reload();
			}
		});

		WCF.Language.addObject({
			'cms.acp.content.add': '{lang}cms.acp.content.add{/lang}',
			'cms.acp.content.revision.action.create': '{lang}cms.acp.content.revision.action.create{/lang}',
			'cms.acp.content.revision.action.update': '{lang}cms.acp.content.revision.action.update{/lang}',
			'cms.acp.content.revision.action.updatePosition': '{lang}cms.acp.content.revision.action.updatePosition{/lang}',
			'cms.acp.content.revision.action.setAsHome': '{lang}cms.acp.content.revision.action.setAsHome{/lang}',
			'cms.acp.content.revision.action.restore': '{lang}cms.acp.content.revision.action.restore{/lang}',
			'cms.acp.content.revision.list': '{lang}cms.acp.content.revision.list{/lang}',
			{foreach from=$objectTypeList item=type}
				'cms.acp.content.type.{$type->objectType}': '{lang}cms.acp.content.type.{$type->objectType}{/lang}',
			{/foreach}
			'cms.acp.content.type.content': '{lang}cms.acp.content.type.content{/lang}'
		});
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}cms.acp.content.list{/lang}</h1>
	<p>{lang includeSelf=true}cms.page.parents{/lang}</p>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			<li class="dropdown">
				<a class="button dropdownToggle"><span class="icon icon16 icon-sort"></span> <span>{lang}cms.acp.page.button.choose{/lang}</span></a>
				<div class="dropdownMenu">
					<ul class="scrollableDropdownMenu">
						{foreach from=$pageList item=node}
							<li{if $node->pageID == $pageID} class="active"{/if}><a href="{link application='cms' controller='ContentList' pageID=$node->pageID}{/link}">{@'&nbsp;&nbsp;&nbsp;&nbsp;'|str_repeat:$pageList->getDepth()}{$node->getTitle()}</a></li>
						{/foreach}
					</ul>
				</div>
			</li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $pageID}
	<div class="jsClipboardContainer tabMenuContainer" data-type="de.codequake.cms.content">
		<nav class="tabMenu">
			<ul>
				<li><a href="{@$__wcf->getAnchor('body')}">{lang}cms.acp.content.position.position.body{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('sidebar')}">{lang}cms.acp.content.position.position.sidebar{/lang}</a></li>

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
								<li class="content jsClipboardObject jsContentRow sortableNode" data-object-id="{$content->contentID}">
									<span class="sortableNodeLabel">
										<span class="title">
											<input type="checkbox" class="jsClipboardItem" data-object-id="{@$content->contentID}" />
											<span class="pointer collapsibleButton icon icon16 {$content->getIcon()}"></span>
											<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}position=body{/link}">{@$content->getTitle()}</a> - <small>{lang}cms.acp.content.type.{$content->getTypeName()}{/lang}</small>
										</span>
										<span class="statusDisplay buttons">
											<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
											<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$content->contentID}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span>
											<span class="icon icon16 icon-check{if $content->isDisabled}-empty{/if} jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if !$content->isDisabled}disable{else}enable{/if}{/lang}" data-object-id="{@$content->contentID}"></span>
											<span class="icon icon16 icon-plus jsContentAddButton jsTooltip pointer" title="{lang}cms.acp.page.content.add{/lang}" data-object-id="{@$content->pageID}" data-position="body" data-parent-id="{$content->contentID}"></span>
											<span class="icon icon16 icon-copy jsCopyButton jsTooltip pointer" title="{lang}cms.acp.content.copy{/lang}" data-object-id="{@$content->contentID}"></span>
											<span class="icon icon16 icon-tasks jsRevisionsButton jsTooltip pointer" title="{lang}cms.acp.content.revision.list{/lang}" data-object-id="{@$content->contentID}"></span>

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
						<button class="button jsContentAddButton" data-object-id="{$page->pageID}" data-position="body"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></button>
				</div>
			{hascontentelse}
				<p class="info">{lang}wcf.global.noItems{/lang}</p>
				<div class="formSubmit">
						<button class="button jsContentAddButton" data-object-id="{$page->pageID}" data-position="body"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></button>
				</div>
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
								<li class="content jsClipboardObject jsContentRow sortableNode" data-object-id="{$content->contentID}">
									<span class="sortableNodeLabel">
										<span class="title">
											<input type="checkbox" class="jsClipboardItem" data-object-id="{@$content->contentID}" />
											<span class="pointer collapsibleButton icon icon16 {$content->getIcon()}"></span>
											<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}position=sidebar{/link}">{@$content->getTitle()}</a> - <small>{lang}cms.acp.content.type.{$content->getTypeName()}{/lang}</small>
										</span>
										<span class="statusDisplay buttons">
											<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
											<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$content->contentID}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span>
											<span class="icon icon16 icon-plus jsContentAddButton jsTooltip pointer" title="{lang}cms.acp.page.content.add{/lang}" data-object-id="{@$content->pageID}" data-position="sidebar" data-parent-id="{$content->contentID}"></span>
											<span class="icon icon16 icon-copy jsCopyButton jsTooltip pointer" title="{lang}cms.acp.content.copy{/lang}" data-object-id="{@$content->contentID}"></span>
											<span class="icon icon16 icon-tasks jsRevisionsButton jsTooltip pointer" title="{lang}cms.acp.content.revision.list{/lang}" data-object-id="{@$content->contentID}"></span>

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
						<button class="button jsContentAddButton" data-object-id="{$page->pageID}" data-position="sidebar"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></button>
				</div>
			{hascontentelse}
				<p class="info">{lang}wcf.global.noItems{/lang}</p>
				<div class="formSubmit">
					<button class="button jsContentAddButton" data-object-id="{$page->pageID}" data-position="sidebar"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></button>
				</div>
			{/hascontent}
		</div>
	</div>

	<div class="contentNavigation">
		<nav class="jsClipboardEditor" data-types="[ 'de.codequake.cms.content' ]"></nav>
	</div>
{/if}

{include file='footer'}
