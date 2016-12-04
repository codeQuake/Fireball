{include file='documentHeader'}

<head>
	<title>{lang}cms.page.sitemap{/lang} - {PAGE_TITLE|language}</title>

	{include file='headInclude'}
	<link rel="canonical" href="{link controller='Sitemap' application='cms'}{/link}" />
</head>

<body id="tpl_{$templateNameApplication}_{$templateName}" data-template="{$templateName}" data-application="{$templateNameApplication}">

{capture assign='headerNavigation'}{/capture}

{include file='header'}

<header class="boxHeadline">
	<h1>{lang}cms.page.sitemap{/lang}</h1>
</header>

{include file='userNotice'}

<div class="container containerPadding marginTop">
	{assign var=oldDepth value=0}
	<ul class="sitemapList">
		{foreach from=$pageNodeTree item=node}
			{section name=i loop=$oldDepth-$pageNodeTree->getDepth()}</ul></li>{/section}
			<li>
				<a href="{$node->getLink()}" style="padding-left: calc({$pageNodeTree->getDepth() + 1} * 14px);">
					{$node->getTitle()}
					<span style="float: right;">{if !$node->lastEditTime|empty}{@$node->lastEditTime|date}{else}{@$node->creationTime|date}{/if}</span>
				</a>
				<ul>
				{if !$pageNodeTree->current()->hasChildren()}
					</ul></li>
				{/if}
				{assign var=oldDepth value=$pageNodeTree->getDepth()}
		{/foreach}
		{section name=i loop=$oldDepth}</ul></li>{/section}
	</ul>
</div>

{include file='footer'}

</body>
</html>
