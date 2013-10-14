{include file='documentHeader'}

<head>
    <title>{$page->getTitle()|language} - {PAGE_TITLE}</title>
    
    {include file='pageHeadInclude' application='cms' sandbox=false}
    <link rel="canonical" href="{link application='cms' controller='Page' object=$page}{/link}" />
</head>

<body id="{$templateName|ucfirst}_{$page->getTitle()|language}">

{include file='header' sidebarOrientation='right'}


<header class="boxHeadline">

	<h1>{$page->getTitle()|language}</h1>

</header>
    
    {foreach from=$contentList item=content}
        <div {if $content->cssID != ''}id="{$content->cssID}"{/if} {if $content->cssClasses != ''}class="contentItem {$content->cssClasses}"{/if}>
            {foreach from=$content->getSections() item=section}
                <div {if $section->cssID != ''}id="{$section->cssID}"{/if} {if $section>cssClasses != ''} class="contentSectionItem {$section->cssClasses}"{/if}>
                    {@$section->getOutput()|language}
                </div>
            {/foreach}
        </div>
    {/foreach}


{include file='footer' sandbox=false}
</body>
</html>