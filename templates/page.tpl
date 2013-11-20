{include file='documentHeader'}

<head>
    <title>{if $__wcf->getPageMenu()->getLandingPage()->menuItem != $page->title}{$page->getTitle()|language} - {/if}{PAGE_TITLE}</title>
    
    {include file='headInclude' application='wcf' sandbox=false}
	{@$page->getLayout()}
    <script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Moderation{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
	
    <link rel="canonical" href="{link application='cms' controller='Page' object=$page}{/link}" />
</head>

<body id="{$templateName|ucfirst}">

{if $page->showSidebar == 1}
	{capture assign='sidebar'}
	{foreach from=$sidebarList item=content}
        <fieldset {if $content->cssID != ''}id="{$content->cssID}"{/if} class="contentItem dashboardBox{if $content->cssClasses != ''}{$content->cssClasses}{/if}">
			<legend>{$content->getTitle()|language}</legend>
            {foreach from=$content->getSections() item=section}
                <div {if $section->cssID != ''}id="{$section->cssID}"{/if} {if $section>cssClasses != ''} class="contentSectionItem {$section->cssClasses}"{/if}>
                    {@$section->getOutput()|language}
                </div>
            {/foreach}
        </fieldset>
    {/foreach}
		{@$__boxSidebar}
	{/capture}
{/if}

{include file='header' sidebarOrientation='left'}


<header class="boxHeadline">

	<h1>{$page->getTitle()|language}</h1>

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