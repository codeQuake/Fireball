{include file='documentHeader'}
<head>
	<title>{lang}cms.page.news.archive{/lang} - {PAGE_TITLE|language}</title>

	{include file='headInclude' application='wcf'}
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new CMS.News.MarkAllAsRead();
			var deleteAction = new WCF.Action.Delete('cms\\data\\news\\NewsAction', '.jsNewsRow');
			var actionObjects = { };
			actionObjects['de.codequake.cms.news'] = { };
			actionObjects['de.codequake.cms.news']['delete'] = deleteAction;
			WCF.Clipboard.init('cms\\page\\NewsArchivePage', {@$hasMarkedItems}, actionObjects);


			});
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">
{capture assign='headerNavigation'}
	<li>
		<a rel="alternate" href="{if $__wcf->getUser()->userID}{link application='cms' controller='NewsFeed' appendSession=false}at={@$__wcf->getUser()->userID}-{@$__wcf->getUser()->accessToken}{/link}{else}{link application='cms' controller='NewsFeed' appendSession=false}{/link}{/if}" title="{lang}wcf.global.button.rss{/lang}" class="jsTooltip">
			<span class="icon icon16 icon-rss"></span> <span class="invisible">{lang}wcf.global.button.rss{/lang}</span>
		</a>
	</li>
	<li class="jsOnly">
		<a title="{lang}cms.news.markAllAsRead{/lang}" class="markAllAsReadButton jsTooltip">
			<span class="icon icon16 icon-ok"></span>
			<span class="invisible">{lang}cms.news.markAllAsRead{/lang}</span>
		</a>
	</li>
{/capture}
{include file='header' sidebarOrientation='right'}

<header class="boxHeadline">
		<h1>{lang}cms.page.news.archive{/lang}</h1>
</header>

{include file='userNotice'}

<div class="contentNavigation">
  {pages print=true assign=pagesLinks controller="NewsArchive" application="cms" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
</div>

	{if $objects|count}
		{include file='newsTable' application='cms'}
	{else}
		<p class="info">{lang}wcf.global.noItems{/lang}</p>
	{/if}


{if $objects|count}
<div class="contentNavigation">
  {if $__wcf->user->userID && $__wcf->session->getPermission('mod.cms.news.canModerateNews')}<nav class="jsClipboardEditor" data-types="[ 'de.codequake.cms.news' ]"></nav>{/if}

  {pages print=true assign=pagesLinks controller="NewsArchive" application="cms" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
</div>
{/if}

{include file='footer' sandbox=false}
</body>
</html>
