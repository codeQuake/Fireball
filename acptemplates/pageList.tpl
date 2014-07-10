{include file='header' pageTitle='cms.acp.page.list'}

<header class="boxHeadline">
	<h1>{lang}cms.acp.page.list{/lang}</h1>

	<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js"></script>
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Action.NestedDelete('cms\\data\\page\\PageAction', '.jsPageRow');
			new WCF.Sortable.List('pageList', 'cms\\data\\page\\PageAction');
			new CMS.ACP.Copy('.jsCopyButton', 'cms\\data\\page\\PageAction');
			new CMS.ACP.Page.AddContent();
			new CMS.ACP.Page.SetAsHome();
			new CMS.ACP.Page.Revisions();
			WCF.Language.addObject({
			{foreach from=$objectTypeList item=type}
				'cms.acp.content.type.{$type->objectType}': '{lang}cms.acp.content.type.{$type->objectType}{/lang}',
			{/foreach}
			'cms.acp.content.type.content': '{lang}cms.acp.content.type.content{/lang}',
			'cms.acp.page.revisions': '{lang}cms.acp.page.revisions{/lang}',
			'cms.acp.page.revision.action.create': '{lang}cms.acp.page.revision.action.create{/lang}',
			'cms.acp.page.revision.action.update': '{lang}cms.acp.page.revision.action.update{/lang}',
			'cms.acp.page.revision.action.updatePosition': '{lang}cms.acp.page.revision.action.updatePosition{/lang}',
			'cms.acp.page.revision.action.setAsHome': '{lang}cms.acp.page.revision.action.setAsHome{/lang}',
			'cms.acp.page.revision.action.restore': '{lang}cms.acp.page.revision.action.restore{/lang}'
			});
		});
		//]]>
	</script>
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
	<section id="pageList" class="sortableListContainer container containerPadding marginTop">
		<ol class="pageList sortableList" data-object-id="0">
			{content}
				{assign var=oldDepth value=0}
				{foreach from=$pageList item=page}
					{section name=i loop=$oldDepth-$pageList->getDepth()}</ol></li>{/section}
					<li class="page jsPageRow sortableNode" data-object-id="{$page->pageID}">
						<span class="sortableNodeLabel">
							<span class="title">
								{if $page->isHome}
								<span class="icon icon16 icon-home jsTooltip" title="{lang}cms.acp.page.homePage{/lang}"></span>
								{else}<span class="pointer collapsibleButton icon icon16 icon-file-text-alt"></span>
								{/if}
								<a href="{link controller='PageEdit' application='cms' object=$page}{/link}">{@$page->getTitle()}</a> - <small>/{$page->getAlias()}/</small>
							</span>
							<span class="statusDisplay buttons">
								<a href="{link controller='PageEdit' application='cms' object=$page}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
								<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$page->pageID}" data-confirm-message="{lang}cms.acp.page.delete.sure{/lang}"></span>
								{if !$page->isHome && !$page->isChild()}<span class="icon icon16 icon-home jsTooltip jsSetAsHome pointer" data-object-id="{$page->pageID}" title="{lang}cms.acp.page.setAsHome{/lang}"></span>{/if}
								<span class="icon icon16 icon-copy jsCopyButton jsTooltip pointer" title="{lang}cms.acp.page.copy{/lang}" data-object-id="{@$page->pageID}"></span>
								<span class="icon icon16 icon-tasks jsRevisionsButton jsTooltip pointer" title="{lang}cms.acp.page.revisions{/lang}" data-object-id="{@$page->pageID}"></span>
								<!-- content controls -->
								<span class="icon icon16 icon-plus jsContentAddButton jsTooltip pointer" title="{lang}cms.acp.page.content.add{/lang}" data-object-id="{@$page->pageID}"></span>
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
					<li><span class="icon icon16 icon-home"></span> <span>{lang}cms.acp.page.setAsHome{/lang}</span></li>
					<li><span class="icon icon16 icon-copy"></span> <span>{lang}cms.acp.page.copy{/lang}</span></li>
					<li><span class="icon icon16 icon-tasks"></span> <span>{lang}cms.acp.page.revisions{/lang}</span></li>
					<li><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></li>
					<li><span class="icon icon16 icon-file"></span> <span>{lang}cms.acp.content.listing{/lang}</span></li>
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
