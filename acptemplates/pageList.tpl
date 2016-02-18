{include file='header' pageTitle='cms.acp.page.list'}

<script data-relocate="true">
	require.config({
		paths: { "codeQuake/CMS": "{@$__wcf->getPath('cms')}js/codeQuake/CMS" }
	});
	require(['codeQuake/CMS/Acp/Content/AddDialog'], function(AddDialog) {
		AddDialog();
	});
	require(['codeQuake/CMS/Acp/Page/Revisions'], function(Revisions) {
		Revisions();
	});
</script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.Language.addObject({
			'cms.acp.content.add': '{lang}fireball.acp.content.add{/lang}',
			{foreach from=$objectTypeList item=type}
				'cms.acp.content.type.{$type->objectType}': '{lang}fireball.acp.content.type.{$type->objectType}{/lang}',
			{/foreach}
			'cms.acp.content.type.content': '{lang}fireball.acp.content.type.content{/lang}',
			'cms.acp.page.revision.action.create': '{lang}fireball.acp.page.revision.action.create{/lang}',
			'cms.acp.page.revision.action.update': '{lang}fireball.acp.page.revision.action.update{/lang}',
			'cms.acp.page.revision.action.updatePosition': '{lang}fireball.acp.page.revision.action.updatePosition{/lang}',
			'cms.acp.page.revision.action.setAsHome': '{lang}fireball.acp.page.revision.action.setAsHome{/lang}',
			'cms.acp.page.revision.action.restore': '{lang}fireball.acp.page.revision.action.restore{/lang}',
			'cms.acp.page.revision.list': '{lang}fireball.acp.page.revision.list{/lang}',
		});

		var deleteAction = new WCF.Action.NestedDelete('cms\\data\\page\\PageAction', '.jsPageRow');
		var toggleAction = new WCF.Action.Toggle('cms\\data\\page\\PageAction', '.jsPageRow', '> .sortableNodeLabel > .buttons > .jsToggleButton');

		var actionObjects = { };
		actionObjects['de.codequake.cms.page'] = { };
		actionObjects['de.codequake.cms.page']['disable'] = actionObjects['de.codequake.cms.page']['enable'] = toggleAction;
		actionObjects['de.codequake.cms.page']['delete'] = deleteAction;

		WCF.Clipboard.init('cms\\acp\\page\\PageListPage', {@$hasMarkedItems}, actionObjects);

		new WCF.Sortable.List('pageList', 'cms\\data\\page\\PageAction', 0, {
			isAllowed: function (item, parent) {
				if (parent === null) {
					parent = $('#pageList');
				}

				// check whether there is an other page with the same alias next to this one.
				return (parent.children('ol').children('li.sortableNode[data-alias="'+ item.data('alias') +'"]:not(#'+ item.wcfIdentify() +')').length == 0);
			}
		});

		new WCF.Action.SimpleProxy({
			action: 'copy',
			className: 'cms\\data\\page\\PageAction',
			elements: $('.jsPageRow .jsCopyButton')
		}, {
			success: function() {
				window.location.reload();
			}
		});

		new WCF.Action.SimpleProxy({
			action: 'setAsHome',
			className: 'cms\\data\\page\\PageAction',
			elements: $('.jsPageRow .jsSetAsHome')
		}, {
			success: function() {
				window.location.reload();
			}
		});
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}fireball.acp.page.list{/lang}</h1>
	<p>{lang}fireball.acp.page.list.description{/lang}</p>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='PageAdd' application='cms'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}fireball.acp.page.add{/lang}</span></a></li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{hascontent}
	<section id="pageList" class="sortableListContainer container containerPadding marginTop jsClipboardContainer" data-type="de.codequake.cms.page">
		<ol class="pageList sortableList" data-object-id="0">
			{content}
				{assign var=oldDepth value=0}
				{foreach from=$pageList item=page}
					{section name=i loop=$oldDepth-$pageList->getDepth()}</ol></li>{/section}
					<li class="page jsPageRow sortableNode jsClipboardObject" data-alias="{$page->alias}" data-object-id="{$page->pageID}">
						<span class="sortableNodeLabel">
							<span class="title">
								<input type="checkbox" class="jsClipboardItem" data-object-id="{@$page->pageID}" />
								{if $page->isHome}
									<span class="icon icon16 fa-home jsTooltip" title="{lang}fireball.acp.page.homePage{/lang}"></span>
								{/if}
								<a href="{link controller='PageEdit' application='cms' object=$page}{/link}">{@$page->getTitle()}</a> - <small>/{$page->getAlias()}/</small>
								{if !$page->isPublished}
									- <small>{lang}fireball.acp.page.delayedPublication{/lang}</small>
								{elseif $page->deactivationDate}
									- <small>{lang}fireball.acp.page.delayedDeactivation{/lang}</small>
								{/if}
							</span>
							<span class="statusDisplay buttons">
								<a href="{link controller='PageEdit' application='cms' object=$page}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
								{if $page->canDelete()}
									<span class="icon icon16 fa-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$page->pageID}" data-confirm-message="{lang}fireball.acp.page.delete.sure{/lang}"></span>
								{else}
									<span class="icon icon16 fa-remove disabled"></span>
								{/if}
								{if !$page->isHome && !$page->isChild()}<span class="icon icon16 fa-home jsTooltip jsSetAsHome pointer" data-object-id="{$page->pageID}" title="{lang}fireball.acp.page.setAsHome{/lang}"></span>{/if}
								<span class="icon icon16 fa-check{if $page->isDisabled}-empty{/if} jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if !$page->isDisabled}disable{else}enable{/if}{/lang}" data-object-id="{@$page->pageID}"></span>
								<span class="icon icon16 fa-copy jsCopyButton jsTooltip pointer" title="{lang}fireball.acp.page.copy{/lang}" data-object-id="{@$page->pageID}"></span>
								<span class="icon icon16 fa-tasks jsRevisionsButton jsTooltip pointer" title="{lang}fireball.acp.page.revision.list{/lang}" data-object-id="{@$page->pageID}"></span>

								<!-- content controls -->
								<span class="icon icon16 fa-plus jsContentAddButton jsTooltip pointer" title="{lang}fireball.acp.content.add{/lang}" data-object-id="{@$page->pageID}" data-position="body"></span>
								<a href="{link controller='ContentList' pageID=$page->pageID application='cms'}{/link}" title="{lang}fireball.acp.page.content.list{/lang}" class="jsTooltip"><span class="icon icon16 fa-file"></span></a>
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

		<div class="formSubmit">
			<button data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
		</div>
	</section>

	<div class="contentNavigation">
		<nav>
			<ul>
				<li><a href="{link controller='PageAdd' application='cms'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}fireball.acp.page.add{/lang}</span></a></li>

				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>

		<nav class="jsClipboardEditor" data-types="[ 'de.codequake.cms.page' ]"></nav>
	</div>
{hascontentelse}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/hascontent}

{include file='footer'}
