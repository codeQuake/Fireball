{include file='documentHeader'}
<head>
	<title>{$news->getTitle()|language} - {PAGE_TITLE|language}</title>

	<link rel="canonical" href="{link application='cms' controller='News' object=$news}{/link}" />
	{include file='headInclude' application='wcf'}
	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Moderation{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
	<script data-relocate="true" src="{@$__wcf->getPath('cms')}js/CMS.js?v={@$__wcfVersion}"></script>
	<script data-relocate="true">
		//<![CDATA[
		$(function () {
			WCF.Language.addObject({
				'wcf.message.share': '{lang}wcf.message.share{/lang}',
				'wcf.message.share.facebook': '{lang}wcf.message.share.facebook{/lang}',
				'wcf.message.share.google': '{lang}wcf.message.share.google{/lang}',
				'wcf.message.share.permalink': '{lang}wcf.message.share.permalink{/lang}',
				'wcf.message.share.permalink.bbcode': '{lang}wcf.message.share.permalink.bbcode{/lang}',
				'wcf.message.share.permalink.html': '{lang}wcf.message.share.permalink.html{/lang}',
				'wcf.message.share.reddit': '{lang}wcf.message.share.reddit{/lang}',
				'wcf.message.share.twitter': '{lang}wcf.message.share.twitter{/lang}',
				'cms.news.ipAddress.title': '{lang}cms.news.ipAddress.title{/lang}',
				'cms.news.ipAddress.news': '{lang}cms.news.ipAddress.news{/lang}',
				'cms.news.ipAddress.otherUsers': '{lang}cms.news.ipAddress.otherUsers{/lang}',
				'cms.news.ipAddress.author' : '{lang}cms.news.ipAddress.author{/lang}'
			});
			new WCF.Action.Delete('cms\\data\\news\\NewsAction', '.jsNews');
			new WCF.Message.Share.Content();
			{if LOG_IP_ADDRESS && $__wcf->session->getPermission('admin.user.canViewIpAddress')}new CMS.News.IPAddressHandler();{/if}
			{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike')}new CMS.News.Like({if $__wcf->getUser()->userID && $__wcf->getSession()->getPermission('user.like.canLike')}1{else}0{/if}, {@LIKE_ENABLE_DISLIKE}, {@LIKE_SHOW_SUMMARY}, {@LIKE_ALLOW_FOR_OWN_CONTENT});{/if}
		});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">

{capture assign='sidebar'}
	<fieldset>
		<legend>{lang}cms.news.author{/lang}</legend>
		<div class="box32">
			<div class="userAvatar">
				<a class="framed userLink" data-user-id="{$news->getUserProfile()->userID}" href="{link controller='User' object=$news->getUserProfile()}{/link}">{@$news->getUserProfile()->getAvatar()->getImageTag(24)}</a>
			</div>
			<div class="userDetails">
				<div class="containerHeadline">
					<h3><a class="userLink" data-user-id="{$news->getUserProfile()->userID}" href="{link controller='User' object=$news->getUserProfile()}{/link}">{$news->getUserProfile()->username}</a></h3>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend>{lang}cms.news.general{/lang}</legend>
		<dl class="plain inlineDataList">
			<dt>{lang}cms.news.clicks{/lang}</dt>
			<dd>{$news->clicks}</dd>
			<dt>{lang}cms.news.comments{/lang}</dt>
			<dd>{@$commentList->countObjects()}</dd>
		</dl>
	</fieldset>
	{if $news->getCategories()|count}
		<fieldset>
			<legend>{lang}cms.news.category.categories{/lang}</legend>

			<ul>
				{foreach from=$news->getCategories() item=category}
					<li><a href="{link application='cms' controller='NewsCategoryList' object=$category}{/link}" class="jsTooltip" title="{lang}cms.news.categorizedNews{/lang}">{$category->getTitle()}</a></li>
				{/foreach}
			</ul>
		</fieldset>
	{/if}
	{if $tags|count}
		<fieldset>
			<legend>{lang}wcf.tagging.tags{/lang}</legend>
			<ul class="tagList">
			{foreach from=$tags item=tag}
				<li><a href="{link controller='Tagged' object=$tag}objectType=de.codequake.cms.news{/link}" class="badge tag jsTooltip" title="{lang}wcf.tagging.taggedObjects.de.codequake.cms.news{/lang}">{$tag->name}</a></li>
			{/foreach}
		</fieldset>
	{/if}
	{event name='boxes'}
	{@$__boxSidebar}
{/capture}
{if !$anchor|isset}{assign var=anchor value=$__wcf->getAnchor('top')}{/if}

{include file='header' sidebarOrientation='right'}

<header class="boxHeadline">
		<h1>{$news->getTitle()|language}</h1>
		<p>{if $news->teaser != ''}{$news->teaser}{else}{@$news->getExcerpt()}{/if}</p>
</header>
{if $news->isDisabled}<p class="warning">{lang}cms.news.publication.delayed{/lang}</p>{/if}
{include file='userNotice'}
{if ($news->isDisabled && $news->canSeeDelayed()) || !$news->isDisabled}
<ul class="messageList">
	<li>
			<article class="message messageReduced marginTop jsNews jsMessage" data-user-id="{$news->userID}" data-object-id="{$news->newsID}" data-news-id="{$news->newsID}" data-is-deleted="{$news->isDeleted}" data-is-disabled="{$news->isDisabled}" data-object-type="de.codequake.cms.likeableNews" data-like-liked="{if $newsLikeData[$news->newsID]|isset}{@$newsLikeData[$news->newsID]->liked}{/if}" data-like-likes="{if $newsLikeData[$news->newsID]|isset}{@$newsLikeData[$news->newsID]->likes}{else}0{/if}" data-like-dislikes="{if $newsLikeData[$news->newsID]|isset}{@$newsLikeData[$news->newsID]->dislikes}{else}0{/if}" data-like-users='{if $newsLikeData[$news->newsID]|isset}{ {implode from=$newsLikeData[$news->newsID]->getUsers() item=likeUser}"{@$likeUser->userID}": { "username": "{$likeUser->username|encodeJSON}" }{/implode} }{else}{ }{/if}'>
				<div>
					{assign var='objectID' value=$news->newsID}
					<section class="messageContent">
						<div>
							<header class="messageHeader">
								<div class="messageHeadline">
										<h1>
											<a href="{link controller='News' object=$news application='cms'}{/link}">{$news->getTitle()}</a>
										</h1>
										<p>
											<span class="username">
												<a class="userLink" data-user-id="{$news->userID}" href="{link controller='User' object=$news->getUserProfile()}{/link}">
													{$news->username}
												</a>
											</span>
											<a class="permalink" href="{link controller='News' object=$news application='cms'}{/link}">
												{@$news->time|time}
											</a>
										</p>
								</div>
							</header>
							<div class="messageBody">
								{if CMS_NEWS_NEWS_IMAGES_ATTACHED && $news->imageID != 0}
								<div class="newsBox256">
									<div class="framed">
										<img src="{@$news->getImage()->getURL()}" alt="{$news->getImage()->title}" style="width: 250px;" />
									</div>
									<div class="newsText">
										{@$news->getFormattedMessage()}
									</div>
								</div>
								{if $news->getPoll()}
									<div>
										{include file='poll' poll=$news->getPoll()}
									</div>
								{/if}
								{else}
								<div>
									{if $news->getPoll()}
									<div>
										{include file='poll' poll=$news->getPoll()}
									</div>
									{/if}
										<div class="newsText">
											{@$news->getFormattedMessage()}
										</div>
								</div>
								{/if}
								{include file='attachments'}
								{if $news->showSignature && $news->getUserProfile()->showSignature() && CMS_NEWS_SIGNATURES}
									<div class="messageSignature">
										<div>{@$news->getUserProfile()->getSignature()}</div>
									</div>
								{/if}
							<div class="messageFooter"></div>
								<footer class="messageOptions">
									<nav class="buttonGroupNavigation jsMobileNavigation">
										<ul class="smallButtons buttonGroup">
											{if $news->canModerate()}<li><a href="{link controller='NewsEdit' application='cms' object=$news}{/link}" class="button jsMessageEditButton" title="{lang}wcf.global.button.edit{/lang}"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}
											{if LOG_IP_ADDRESS && $news->ipAddress && $__wcf->session->getPermission('admin.user.canViewIpAddress')}<li class="jsIpAddress jsOnly" data-news-id="{@$news->newsID}"><a title="{lang}cms.news.ipAddress{/lang}" class="button jsTooltip"><span class="icon icon16 icon-globe"></span> <span class="invisible">{lang}cms.news.ipAddress{/lang}</span></a></li>{/if}											{if $news->canModerate()}<li class="jsOnly"><div class="button"><span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$news->newsID}" data-confirm-message="{lang}cms.news.delete.sure{/lang}"></span></div></li>{/if}
											{event name='messageOptions'}
											<li class="toTopLink"><a href="{@$anchor}" title="{lang}wcf.global.scrollUp{/lang}" class="button jsTooltip"><span class="icon icon16 icon-arrow-up"></span> <span class="invisible">{lang}wcf.global.scrollUp{/lang}</span></a></li>

										</ul>
									</nav>
								</footer>
							</div>
						</div>
					</section>
				</div>
			</article>
		</li>
	</ul>
	<div class="contentNavigation">
		<nav>
			<ul>
				<li><a href="{link application='cms' controller='News' object=$news appendSession=false}{/link}" class="button jsButtonShare jsOnly" data-link-title="{$news->subject}"><span class="icon icon16 icon-link"></span> <span>{lang}wcf.message.share{/lang}</span></a></li>
			{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
		{if ENABLE_SHARE_BUTTONS}
		{include file='shareButtons'}
		{/if}
	</div>
	{if CMS_NEWS_COMMENTS}
		{include file='newsCommentList' application='cms'}
	{/if}
{/if}
{include file='footer' sandbox=false}
</body>
</html>
