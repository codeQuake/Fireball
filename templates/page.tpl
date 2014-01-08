{include file='documentHeader'}

<head>
    <title>{if $__wcf->getPageMenu()->getLandingPage()->menuItem != $page->title}{$page->getTitle()|language} - {/if}{PAGE_TITLE}</title>
    
    {include file='headInclude' application='wcf' sandbox=false}
	{@$page->getLayout()}
    <script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Moderation{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
	
    <link rel="canonical" href="{link application='cms' controller='Page' object=$page}{/link}" />
</head>

<body id="{$templateName|ucfirst}">
{if $sidebarList|count || $page->showSidebar}
{capture assign='sidebar'}
	{foreach from=$sidebarList item=content}
        <div {if $content->cssID != ''}id="{$content->cssID}"{/if} class="contentItem {if $content->cssClasses != ''}{$content->cssClasses}{/if}">
            {foreach from=$content->getSections() item=section}
				{if $section->getObjectType() == 'de.codequake.cms.section.type.dashboard'}
				 <div {if $section->cssID != ''}id="{$section->cssID}"{/if} class="contentSectionItem {if $section>cssClasses != ''} {$section->cssClasses}{/if}">
                    {@$section->getOutput()|language}
                </div>
				{else}
                <fieldset {if $section->cssID != ''}id="{$section->cssID}"{/if} class="contentSectionItem  dashboardBox{if $section>cssClasses != ''} {$section->cssClasses}{/if}">
					<legend></legend>
                    {@$section->getOutput()|language}
                </fieldset>
				{/if}
            {/foreach}
		</div>
    {/foreach}
	
	{if $page->showSidebar == 1}
		{@$__boxSidebar}
	{/if}
{/capture}
{/if}

{include file='header' sidebarOrientation=$page->sidebarOrientation}


<header class="boxHeadline">
{if $__wcf->getPageMenu()->getLandingPage()->menuItem == $page->getTitle()}

		<h1>{PAGE_TITLE|language}</h1>
		{hascontent}<p>{content}{PAGE_DESCRIPTION|language}{/content}</p>{/hascontent}

{else}
	<h1>{$page->getTitle()|language}</h1>
	<p>{$page->description|language}</p>
{/if}
</header>
{include file='userNotice'}

    {foreach from=$bodyList item=content}
        <div {if $content->cssID != ''}id="{$content->cssID}"{/if} {if $content->cssClasses != ''}class="contentItem {$content->cssClasses}"{/if}>
            {foreach from=$content->getSections() item=section}
                <div {if $section->cssID != ''}id="{$section->cssID}"{/if} {if $section>cssClasses != ''} class="contentSectionItem {$section->cssClasses}"{/if}>
                    {@$section->getOutput()|language}
                </div>
            {/foreach}
        </div>
    {/foreach}

	{if $page->isCommentable}
    {include file='pageCommentList' application='cms'}
	{/if}
{include file='footer' sandbox=false}
</body>
</html>