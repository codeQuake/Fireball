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
			<p class="contentHeaderDescription">
				{if $page->description}{@$page->description|language}{/if}
			</p>
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
	</header>
{/capture}

{hascontent}
	{capture assign='sidebarRight'}
		{content}
			{assign var=oldDepth value=0}
			{foreach from=$sidebarNodeTree item=content}
				{if $content->getTypeName() != 'de.codequake.cms.content.type.dashboard'}
					{section name=i loop=$oldDepth-$sidebarNodeTree->getDepth()}</fieldset>{/section}
					<fieldset class="dashboardBox{if $content->getCSSClasses() != ""} {$content->getCSSClasses()}{/if}" id="cmsContent{@$content->contentID}" data-content-type="{$content->getTypeName()}">
						<legend>{$content->getTitle()}</legend>

						{@$content->getOutput()|language}
						{if !$sidebarNodeTree->current()->hasChildren()}
							</fieldset>
						{/if}

						{assign var=oldDepth value=$sidebarNodeTree->getDepth()}
				{else}
					{@$content->getOutput()|language}
				{/if}
			{/foreach}
			{section name=i loop=$oldDepth}</fieldset>{/section}

			{event name='boxes'}
		{/content}
	{/capture}
{/hascontent}

{include file='header' sidebarOrientation=$page->sidebarOrientation}

{if !$page->isPublished && $page->publicationDate}
	<p class="info">{lang}cms.page.delayedPublication{/lang}</p>
{/if}

{assign var=oldDepth value=0}
{foreach from=$contentNodeTree item=content}
	{section name=i loop=$oldDepth-$contentNodeTree->getDepth()}</div>{/section}
	<div{if $content->getCSSClasses() != ""} class="{$content->getCSSClasses()}"{/if} id="cmsContent{@$content->contentID}" data-content-type="{$content->getTypeName()}">
		{@$content->getOutput()|language}
		{if !$contentNodeTree->current()->hasChildren()}
			</div>
		{/if}
		{assign var=oldDepth value=$contentNodeTree->getDepth()}
{/foreach}

{section name=i loop=$oldDepth}</div>{/section}

{if $page->isCommentable && $page->getPermission('canViewComment')}
	<header id="comments" class="boxHeadline boxSubHeadline">
		<h2>{lang}cms.page.comments{/lang} <span class="badge">{@$commentList->countObjects()}</span></h2>
	</header>

	{include file='__commentJavaScript' commentContainerID='pageCommentList'}

	<div class="container containerList marginTop">
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
				<div class="containerPadding">
					{lang}cms.page.comments.noComments{/lang}
				</div>
			{/hascontent}
		{/if}
	</div>
{/if}

<script data-relocate="true">
	$(function() {
		{if $page->allowSubscribing && $__wcf->user->userID}
			WCF.Language.addObject({
				'wcf.user.objectWatch.manageSubscription': '{lang}wcf.user.objectWatch.manageSubscription{/lang}'
			});

			new WCF.User.ObjectWatch.Subscribe();
		{/if}

		{if $__wcf->getSession()->getPermission('admin.fireball.content.canAddContent')}
			new Fireball.Page.ContentTypes({$page->pageID});
		{/if}
	});
</script>

{include file='footer'}
