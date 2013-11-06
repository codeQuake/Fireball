{include file='documentHeader'}

<head>
	<title>{if $__wcf->getPageMenu()->getLandingPage()->menuItem != 'cms.page.news'}{lang}cms.page.news{/lang} - {/if}{PAGE_TITLE|language}</title>
	
	{include file='headInclude' application='wcf'}

</head>

<body id="tpl{$templateName|ucfirst}">
{capture assign='sidebar'}
	{hascontent}
		<fieldset>
			<legend>{lang}cms.news.category.categories{/lang}</legend>
			
			<ol class="sidebarNestedCategoryList">
				{content}
					{foreach from=$categoryList item=categoryItem}
						<li>
							<a href="{link application='cms' controller='NewsList' object=$categoryItem->getDecoratedObject()}{/link}">{$categoryItem->getTitle()}</a>
							{if $categoryItem->getUnreadNews() != 0}<span class="badge">{#$categoryItem->getUnreadNews()}</span>{/if}
							{if $categoryItem->hasChildren()}
								<ol>
									{foreach from=$categoryItem item=subCategoryItem}
										<li>
											<a href="{link application='cms' controller='NewsList' object=$subCategoryItem->getDecoratedObject()}{/link}">{$subCategoryItem->getTitle()}</a>
											{if $categoryItem->getUnreadNews() != 0}<span class="badge">{#$categoryItem->getUnreadNews()}</span>{/if}
											
										</li>
									{/foreach}
								</ol>
							{/if}
						</li>
					{/foreach}
				{/content}
			</ol>
		</fieldset>
	{/hascontent}

	{event name='boxes'}

	{@$__boxSidebar}
{/capture}

{include file='header' sidebarOrientation='right'}

{if $__wcf->getPageMenu()->getLandingPage()->menuItem == 'cms.page.news'}
	<header class="boxHeadline">
		<h1>{PAGE_TITLE|language}</h1>
		{hascontent}<p>{content}{PAGE_DESCRIPTION|language}{/content}</p>{/hascontent}
	</header>
{else}
	<header class="boxHeadline">
		<h1>{lang}cms.page.news{/lang}</h1>
	</header>
{/if}

{include file='userNotice'}


<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller="NewsCategoryList" application="cms" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	{hascontent}
		<nav>
			<ul>
				{content}
					{if $__wcf->user->userID && $__wcf->session->getPermission('user.cms.news.canAddNews')}<li><a href="{link application='cms' controller='NewsAdd'}{/link}" title="{lang}cms.news.add{/lang}" class="button"><span class="icon icon16 icon-asterisk"></span> <span>{lang}cms.news.add{/lang}</span></a></li>{/if}
					{event name='contentNavigationButtonsTop'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>

{include file='newsListing' application='cms'}

{if $objects|count}
<div class="contentNavigation">
	{pages print=true assign=pagesLinks controller="NewsCategoryList" application="cms"  link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}
	{hascontent}
		<nav>
			<ul>
				{content}
					{if $__wcf->user->userID && $__wcf->session->getPermission('user.cms.news.canAddNews')}<li><a href="{link application='cms' controller='NewsAdd'}{/link}" title="{lang}cms.news.add{/lang}" class="button"><span class="icon icon16 icon-asterisk"></span> <span>{lang}cms.news.add{/lang}</span></a></li>{/if}
					{event name='contentNavigationButtonsBottom'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</div>
{/if}

{include file='footer'}

</body>
</html>