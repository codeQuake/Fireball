{include file='documentHeader'}

<head>
	<title>{if $__wcf->getPageMenu()->getLandingPage()->menuItem != 'cms.page.news'}{lang}cms.page.news{/lang} - {/if}{PAGE_TITLE|language}</title>
	
	{include file='headInclude' application='wcf'}

</head>

<body id="tpl{$templateName|ucfirst}">


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

<div class="container containerPadding marginTop">
	<fieldset>
		<legend>{lang}cms.news.category.categories{/lang}</legend>

		<ol class="nestedCategoryList doubleColumned">
			{foreach from=$categoryList item=categoryItem}
				<li data-category-id="{@$categoryItem->categoryID}">
					<div>
						<div class="containerHeadline">
							<h3><a href="{link application='cms' controller='NewsList' object=$categoryItem->getDecoratedObject()}{/link}"{if $categoryItem->getDescription()} class="jsTooltip" title="{$categoryItem->getDescription()}"{/if}>{$categoryItem->getTitle()}</a></h3>
						</div>
						
						{if $categoryItem->hasChildren()}
							<ol>
								{foreach from=$categoryItem item=subCategoryItem}
									<li data-category-id="{@$subCategoryItem->categoryID}">
										<a href="{link application='cms' controller='NewsList' object=$subCategoryItem->getDecoratedObject()}{/link}"{if $subCategoryItem->getDescription()} class="jsTooltip" title="{$subCategoryItem->getDescription()}"{/if}>{$subCategoryItem->getTitle()}</a>
									</li>
								{/foreach}
							</ol>
						{/if}
					</div>
				</li>
			{/foreach}
		</ol>
	</fieldset>
</div>

<div class="contentNavigation">
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


{include file='footer'}

</body>
</html>