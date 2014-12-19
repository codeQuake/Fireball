{include file='header' pageTitle='cms.acp.page.list'}

<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.Language.addObject({
			'cms.acp.content.add': '{lang}cms.acp.content.add{/lang}',
			{foreach from=$objectTypeList item=type}
				'cms.acp.content.type.{$type->objectType}': '{lang}cms.acp.content.type.{$type->objectType}{/lang}',
			{/foreach}
			'cms.acp.content.type.content': '{lang}cms.acp.content.type.content{/lang}',
			'cms.acp.page.revision.action.create': '{lang}cms.acp.page.revision.action.create{/lang}',
			'cms.acp.page.revision.action.update': '{lang}cms.acp.page.revision.action.update{/lang}',
			'cms.acp.page.revision.action.updatePosition': '{lang}cms.acp.page.revision.action.updatePosition{/lang}',
			'cms.acp.page.revision.action.setAsHome': '{lang}cms.acp.page.revision.action.setAsHome{/lang}',
			'cms.acp.page.revision.action.restore': '{lang}cms.acp.page.revision.action.restore{/lang}',
			'cms.acp.page.revision.list': '{lang}cms.acp.page.revision.list{/lang}',
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

		new CMS.ACP.Copy('.jsCopyButton', 'cms\\data\\page\\PageAction');
		new CMS.ACP.Page.AddContent();
		new CMS.ACP.Page.SetAsHome();
		new CMS.ACP.Page.Revisions();
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}cms.acp.page.list{/lang}</h1>
	<p>{lang}cms.acp.page.list.description{/lang}</p>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='PageAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.page.add{/lang}</span></a></li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>

	<nav class="jsClipboardEditor" data-types="[ 'de.codequake.cms.page' ]"></nav>
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
									<span class="icon icon16 icon-home jsTooltip" title="{lang}cms.acp.page.homePage{/lang}"></span>
								{else}
									<span class="icon icon16 icon-file-text-alt"></span>
								{/if}
								<a href="{link controller='PageEdit' application='cms' object=$page}{/link}">{@$page->getTitle()}</a> - <small>/{$page->getAlias()}/</small>
								{if !$page->isPublished}
									- <small>{lang}cms.acp.page.delayedPublication{/lang}</small>
								{elseif $page->deactivationDate}
									- <small>{lang}cms.acp.page.delayedDeactivation{/lang}</small>
								{/if}
							</span>
							<span class="statusDisplay buttons">
								<a href="{link controller='PageEdit' application='cms' object=$page}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
								<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$page->pageID}" data-confirm-message="{lang}cms.acp.page.delete.sure{/lang}"></span>
								{if !$page->isHome && !$page->isChild()}<span class="icon icon16 icon-home jsTooltip jsSetAsHome pointer" data-object-id="{$page->pageID}" title="{lang}cms.acp.page.setAsHome{/lang}"></span>{/if}
								<span class="icon icon16 icon-check{if $page->isDisabled}-empty{/if} jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if !$page->isDisabled}disable{else}enable{/if}{/lang}" data-object-id="{@$page->pageID}"></span>
								<span class="icon icon16 icon-copy jsCopyButton jsTooltip pointer" title="{lang}cms.acp.page.copy{/lang}" data-object-id="{@$page->pageID}"></span>
								<span class="icon icon16 icon-tasks jsRevisionsButton jsTooltip pointer" title="{lang}cms.acp.page.revision.list{/lang}" data-object-id="{@$page->pageID}"></span>

								<!-- content controls -->
								<span class="icon icon16 icon-plus jsContentAddButton jsTooltip pointer" title="{lang}cms.acp.page.content.add{/lang}" data-object-id="{@$page->pageID}" data-position="body"></span>
								<a href="{link controller='ContentList' pageID=$page->pageID application='cms'}{/link}" title="{lang}cms.acp.page.content.list{/lang}" class="jsTooltip"><span class="icon icon16 icon-file"></span></a>
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

	<div class="formSubmit">
		<button class="button buttonPrimary" data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
	</div>

	<div class="container marginTop">
		<ol class="containerList infoBoxList">
			<li class="box32">
				<span class="icon icon-question-sign icon32"></span>
				<div class="containerHeadline">
					<h3>{lang}cms.acp.page.legend{/lang}</h3>
				</div>
				<ul class="dataList">
					<li><span class="icon icon16 icon-pencil"></span> <span>{lang}cms.acp.page.edit{/lang}</span></li>
					<li><span class="icon icon16 icon-remove"></span> <span>{lang}cms.acp.page.remove{/lang}</span></li>
					<li><span class="icon icon16 icon-check"></span> <span>{lang}cms.acp.page.disable{/lang}</span></li>
					<li><span class="icon icon16 icon-home"></span> <span>{lang}cms.acp.page.setAsHome{/lang}</span></li>
					<li><span class="icon icon16 icon-copy"></span> <span>{lang}cms.acp.page.copy{/lang}</span></li>
					<li><span class="icon icon16 icon-tasks"></span> <span>{lang}cms.acp.page.revision.list{/lang}</span></li>
					<li><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></li>
					<li><span class="icon icon16 icon-file"></span> <span>{lang}cms.acp.content.list{/lang}</span></li>
				</ul>
			</li>
			<li class="box32">
				<span class="icon icon32 icon-cloud-download"></span>
				<div class="containerHeadline">
					<h3>{lang}cms.acp.page.backup{/lang}</h3>
				</div>
				<p>{lang}cms.acp.page.backup.description{/lang}</p>
			</li>
		</ol>
	</div>
{hascontentelse}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/hascontent}
{include file='footer'}
