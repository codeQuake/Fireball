{include file='documentHeader'}

<head>
    <title>{if $__wcf->getPageMenu()->getLandingPage()->menuItem != $page->title}{$page->getTitle()|language} - {/if}{PAGE_TITLE|language}</title>

    {include file='headInclude' application='wcf' sandbox=false}
	{@$page->getLayout()}
    <script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Moderation{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>

    <link rel="canonical" href="{$page->getLink()}" />
</head>

<body id="tpl{$templateName|ucfirst}" data-page-id="{$page->pageID}">
{if $page->showSidebar}
{capture assign='sidebar'}
	{if $page->showSidebar == 1}
		{@$__boxSidebar}
	{/if}
{/capture}
{/if}

{include file='header' sidebarOrientation=$page->sidebarOrientation}


<header class="boxHeadline">
{if $__wcf->getPageMenu()->getLandingPage()->menuItem == $page->title}

		<h1>{PAGE_TITLE|language}</h1>
		{hascontent}<p>{content}{PAGE_DESCRIPTION|language}{/content}</p>{/hascontent}

{else}
	<h1>{$page->getTitle()|language}</h1>
	<p>{$page->description|language}</p>
{/if}
</header>
{include file='userNotice'}

	{assign var=oldDepth value=0}
    {foreach from=$contentNodeTree item=content}
    	{section name=i loop=$oldDepth-$contentNodeTree->getDepth()}</div>{/section}
		<div {if $content->getCSSClasses() != ""}class="{$content->getCSSClasses()}"{/if} {if $content->cssID != ""}id="{$content->cssID}"{/if}>
		{@$content->getOutput()|language}
		{if !$contentNodeTree->current()->hasChildren()}
			</div>
		{/if}
		{assign var=oldDepth value=$contentNodeTree->getDepth()}
    {/foreach}
	{section name=i loop=$oldDepth}</div>{/section}

	{if $page->isCommentable}
    {include file='pageCommentList' application='cms'}
	{/if}
{include file='footer' sandbox=false}
</body>
</html>
