{include file='documentHeader'}

<head>
	<title>{if $__wcf->getPageMenu()->getLandingPage()->menuItemID != $page->menuItemID}{$page->getTitle()} - {/if}{PAGE_TITLE|language}</title>

	{include file='headInclude'}
	{foreach from=$page->getStylesheets() item=stylesheet}
		<link rel="stylesheet" type="text/css" href="{$stylesheet->getURL()}" />
	{/foreach}
	<link rel="canonical" href="{$page->getLink(false)}" />

	<script data-relocate="true" src="{@$__wcf->getPath('cms')}js/CMS{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			{if $page->allowSubscribing && $__wcf->user->userID}
				WCF.Language.addObject({
					'wcf.user.objectWatch.manageSubscription': '{lang}wcf.user.objectWatch.manageSubscription{/lang}'
				});

				new WCF.User.ObjectWatch.Subscribe();
			{/if}
		});
		//]]>
	</script>
</head>

<body id="tpl_{$templateNameApplication}_{$templateName}" data-template="{$templateName}" data-application="{$templateNameApplication}" data-page-id="{$page->pageID}">

{capture assign='headerNavigation'}
	{if $page->allowSubscribing && $__wcf->user->userID}
		<li class="jsOnly"><a title="{lang}wcf.user.objectWatch.manageSubscription{/lang}" class="jsSubscribeButton jsTooltip" data-object-type="de.codequake.cms.page" data-object-id="{@$page->pageID}"><span class="icon icon16 icon-bookmark"></span> <span class="invisible">{lang}wcf.user.objectWatch.manageSubscription{/lang}</span></a></li>
	{/if}
{/capture}

{hascontent}
	{capture assign='sidebar'}
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

<header class="boxHeadline">
	{if $__wcf->getPageMenu()->getLandingPage()->menuItemID == $page->menuItemID}
		<h1>{PAGE_TITLE|language}</h1>
		{hascontent}<p>{content}{PAGE_DESCRIPTION|language}{/content}</p>{/hascontent}
	{else}
		<h1>{$page->getTitle()}</h1>
		<p>{$page->description|language}</p>
	{/if}
</header>

{include file='userNotice'}

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

{include file='footer'}

</body>
</html>
