{capture assign='headContent'}
	{foreach from=$page->getStylesheets() item=stylesheet}
		<link rel="stylesheet" type="text/css" href="{$stylesheet->getURL()}" />
	{/foreach}
	<script data-relocate="true" src="{@$__wcf->getPath('cms')}js/Fireball{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
{/capture}

{capture assign='headerNavigation'}
	{if $page->allowSubscribing && $__wcf->user->userID}
		<li class="jsOnly"><a title="{lang}wcf.user.objectWatch.manageSubscription{/lang}" class="jsSubscribeButton jsTooltip" data-object-type="de.codequake.cms.page" data-object-id="{@$page->pageID}"><span class="icon icon16 fa-bookmark"></span> <span class="invisible">{lang}wcf.user.objectWatch.manageSubscription{/lang}</span></a></li>
	{/if}
{/capture}

{capture assign='pageTitle'}{$page->getTitle()}{/capture}

{capture assign='contentHeader'}
	<header class="contentHeader">
		<div class="contentHeaderTitle">
			<h1 class="contentTitle">{$page->getTitle()}</h1>
			{if $page->description}
				<p class="contentHeaderDescription">
					{@$page->description|language}
				</p>
			{/if}
		</div>

		{hascontent}
			<nav class="contentHeaderNavigation">
				<ul>
					{content}
						{event name='contentHeaderNavigation'}
					{/content}
				</ul>
			</nav>
		{/hascontent}

		{hascontent}
			<nav class="contentHeaderNavigation">
				<ul class="jsPageInlineEditorContainer"
				    data-page-id="{@$page->pageID}"
				    data-is-disabled="{@$page->isDisabled}"
				    data-advanced-url="{link controller='PageEdit' object=$page isACP=1 application='cms'}{/link}">
					{content}
						<li><a href="#" class="button jsPageInlineEditor jsOnly"><span class="icon icon16 fa-pencil"></span> <span>{lang}cms.acp.page.edit{/lang}</span></a></li>
						{event name='contentHeaderNavigation'}
					{/content}
				</ul>
			</nav>
		{/hascontent}
	</header>
{/capture}

{assign var=sidebarUc value=$page->sidebarOrientation|ucfirst}
{hascontent}
	{capture assign='sidebar'|concat:$sidebarUc}
		{content}
			{include file='contentNodeList' application='cms' contentNodeTree=$sidebarContentNodeTree position='sidebar'}
			{event name='boxes'}
		{/content}
	{/capture}
{/hascontent}

{include file='header'}

{if !$page->isPublished && $page->publicationDate}
	<p class="info">{lang}cms.page.delayedPublication{/lang}</p>
{/if}

{include file='contentNodeList' application='cms' contentNodeTree=$contentContentNodeTree position='content'}

{if $page->isCommentable && $page->getPermission('canViewComment')}
	<section id="comments" class="section sectionContainerList">
		<h2 class="sectionTitle">{lang}cms.page.comments{/lang} <span class="badge">{@$commentList->countObjects()}</span></h2>

		{include file='__commentJavaScript' commentContainerID='pageCommentList'}

		{if $commentCanAdd}
			<ul id="pageCommentList" class="commentList containerList" data-can-add="true" data-object-id="{@$page->pageID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
				{include file='commentList'}
			</ul>
		{else}
			{hascontent}
				<ul id="pageCommentList" class="commentList containerList" data-can-add="false" data-object-id="{@$page->pageID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
					{content}
						{include file='commentList'}
					{/content}
				</ul>
			{hascontentelse}
				<p class="info">{lang}cms.page.comments.noComments{/lang}</p>
			{/hascontent}
		{/if}
	</section>
{/if}

<script data-relocate="true">
	$(function() {
		require(['Language',], function(Language) {
			Language.addObject({
				'wcf.user.objectWatch.manageSubscription': '{lang}wcf.user.objectWatch.manageSubscription{/lang}',
				'cms.content.add': '{lang}cms.acp.content.add{/lang}',
				'cms.page.edit.start': '{lang}cms.page.edit.start{/lang}',
				'cms.page.edit.finish': '{lang}cms.page.edit.finish{/lang}',
				'cms.page.edit.finish.confirm': '{lang}cms.page.edit.finish.confirm{/lang}',
				'cms.page.edit.save': '{lang}cms.page.edit.save{/lang}',
				'cms.page.edit.acp': '{lang}cms.page.edit.acp{/lang}',
				'cms.page.edit.addContent': '{lang}cms.page.edit.addContent{/lang}'
			});

			{if $page->allowSubscribing && $__wcf->user->userID}
				new WCF.User.ObjectWatch.Subscribe();
			{/if}

			{if $__wcf->getSession()->getPermission('admin.fireball.content.canAddContent')}
				var $inlineEditor = new Fireball.Page.InlineEditor('.jsPageInlineEditorContainer');
				var $updateHandler = new Fireball.Page.UpdateHandler({@$page->pageID});
				$inlineEditor.setUpdateHandler($updateHandler);
				$inlineEditor.setEnvironment('page', {@$page->pageID});
			{/if}
		});
	});
</script>

{include file='footer'}
