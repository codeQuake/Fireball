{include file='header' pageTitle='cms.acp.content.list'}

<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.TabMenu.init();
		new WCF.Action.NestedDelete('cms\\data\\content\\ContentAction', '.jsContentRow');
		new WCF.Action.Toggle('cms\\data\\content\\ContentAction', '.jsContentRow', '> .sortableNodeLabel > .buttons > .jsToggleButton');
		new WCF.Sortable.List('contentListSidebar', 'cms\\data\\content\\ContentAction');
		new WCF.Sortable.List('contentListBody', 'cms\\data\\content\\ContentAction');
		new CMS.ACP.Copy('.jsCopyButton', 'cms\\data\\content\\ContentAction');
		new CMS.ACP.Page.AddContent();
		new CMS.ACP.Content.Revisions();
		WCF.Language.addObject({
			{foreach from=$objectTypeList item=type}
				'cms.acp.content.type.{$type->objectType}': '{lang}cms.acp.content.type.{$type->objectType}{/lang}',
			{/foreach}
			'cms.acp.content.type.content': '{lang}cms.acp.content.type.content{/lang}',
			'cms.acp.content.revisions': '{lang}cms.acp.content.revisions{/lang}',
			'cms.acp.content.revision.action.create': '{lang}cms.acp.content.revision.action.create{/lang}',
			'cms.acp.content.revision.action.update': '{lang}cms.acp.content.revision.action.update{/lang}',
			'cms.acp.content.revision.action.updatePosition': '{lang}cms.acp.content.revision.action.updatePosition{/lang}',
			'cms.acp.content.revision.action.setAsHome': '{lang}cms.acp.content.revision.action.setAsHome{/lang}',
			'cms.acp.content.revision.action.restore': '{lang}cms.acp.content.revision.action.restore{/lang}'
		});
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}cms.acp.content.list{/lang}</h1>
</header>

<form method="post" action="{link application='cms' controller='ContentList'}{/link}">
	<div id="filterContainer" class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.global.filter{/lang}</legend>

			<dl>
				<dt><label for="pageID">{lang}cms.global.page{/lang}</label></dt>
				<dd>
					<select name="pageID" id="pageID">
						{foreach from=$pageList item=node}
							<option value="{@$node->pageID}"{if $node->pageID == $pageID} selected="selected"{/if}>{@'&nbsp;&nbsp;&nbsp;&nbsp;'|str_repeat:$pageList->getDepth()}{$node->getTitle()|language}</option>
						{/foreach}
					</select>
				</dd>
			</dl>
		</fieldset>
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

<div class="contentNavigation">
	<nav>
		<ul>
			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $pageID}
	<div class="tabMenuContainer">
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
								<li class="content jsContentRow sortableNode" data-object-id="{$content->contentID}">
									<span class="sortableNodeLabel">
										<span class="title">
											<span class="pointer collapsibleButton icon icon16 {$content->getIcon()}"></span>
											<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}position=body{/link}">{@$content->getTitle()|language}</a> - <small>{lang}cms.acp.content.type.{$content->getTypeName()}{/lang}</small>
										</span>
										<span class="statusDisplay buttons">
											<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
											<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$content->contentID}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span>
											<span class="icon icon16 icon-check{if $content->isDisabled}-empty{/if} jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if !$content->isDisabled}disable{else}enable{/if}{/lang}" data-object-id="{@$content->contentID}"></span>
											<span class="icon icon16 icon-plus jsContentAddButton jsTooltip pointer" title="{lang}cms.acp.page.content.add{/lang}" data-object-id="{@$content->pageID}" data-position="body" data-parent-id="{$content->contentID}"></span>
											<span class="icon icon16 icon-copy jsCopyButton jsTooltip pointer" title="{lang}cms.acp.content.copy{/lang}" data-object-id="{@$content->contentID}"></span>
											<span class="icon icon16 icon-tasks jsRevisionsButton jsTooltip pointer" title="{lang}cms.acp.page.revisions{/lang}" data-object-id="{@$content->contentID}"></span>

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
						<button class="button jsContentAddButton jsTooltip pointer" data-object-id="{$page->pageID}" data-position="body" title="{lang}cms.acp.content.add{/lang}"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></button>
				</div>
			{hascontentelse}
				<p class="info">{lang}wcf.global.noItems{/lang}</p>
				<div class="formSubmit">
						<button class="button jsContentAddButton jsTooltip pointer" data-object-id="{$page->pageID}" data-position="body" title="{lang}cms.acp.content.add{/lang}"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></button>
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
								<li class="content jsContentRow sortableNode" data-object-id="{$content->contentID}">
									<span class="sortableNodeLabel">
										<span class="title">
											<span class="pointer collapsibleButton icon icon16 {$content->getIcon()}"></span>
											<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}position=sidebar{/link}">{@$content->getTitle()|language}</a> - <small>{lang}cms.acp.content.type.{$content->getTypeName()}{/lang}</small>
										</span>
										<span class="statusDisplay buttons">
											<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
											<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$content->contentID}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span>
											<span class="icon icon16 icon-plus jsContentAddButton jsTooltip pointer" title="{lang}cms.acp.page.content.add{/lang}" data-object-id="{@$content->pageID}" data-position="sidebar" data-parent-id="{$content->contentID}"></span>
											<span class="icon icon16 icon-copy jsCopyButton jsTooltip pointer" title="{lang}cms.acp.content.copy{/lang}" data-object-id="{@$content->contentID}"></span>
											<span class="icon icon16 icon-tasks jsRevisionsButton jsTooltip pointer" title="{lang}cms.acp.page.revisions{/lang}" data-object-id="{@$content->contentID}"></span>

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
						<button class="button jsContentAddButton jsTooltip pointer" data-object-id="{$page->pageID}" data-position="sidebar" title="{lang}cms.acp.content.add{/lang}"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></button>
				</div>
			{hascontentelse}
				<p class="info">{lang}wcf.global.noItems{/lang}</p>
				<div class="formSubmit">
					<button class="button jsContentAddButton jsTooltip pointer" data-object-id="{$page->pageID}" data-position="sidebar" title="{lang}cms.acp.content.add{/lang}"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></button>
				</div>
			{/hascontent}
		</div>
	</div>

	<div class="container marginTop">
		<ol class="containerList infoBoxList">
			<li class="box32">
				<span class="icon icon32 icon-question-sign"></span>
				<div class="containerHeadline">
					<h3>{lang}cms.acp.page.legend{/lang}</h3>
				</div>
				<ul class="dataList">
					<li><span class="icon icon16 icon-pencil"></span> <span>{lang}cms.acp.content.edit{/lang}</span></li>
					<li><span class="icon icon16 icon-remove"></span> <span>{lang}cms.acp.content.remove{/lang}</span></li>
					<li><span class="icon icon16 icon-check"></span> <span>{lang}cms.acp.page.disable{/lang}</span></li>
					<li><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></li>
					<li><span class="icon icon16 icon-copy"></span> <span>{lang}cms.acp.content.copy{/lang}</span></li>
					<li><span class="icon icon16 icon-tasks"></span> <span>{lang}cms.acp.content.revisions{/lang}</span></li>
				</ul>
			</li>
		</ol>
	</div>
{/if}

{include file='footer'}
