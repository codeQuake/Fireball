{include file='documentHeader'}
<head>
	<title>{if $__wcf->getPageMenu()->getLandingPage()->menuItem != 'cms.pageMenu.index'}{lang}cms.pageMenu.index{/lang} - {/if}{PAGE_TITLE|language}</title>
	
	{include file='headInclude' sandbox=false}
	<link rel="canonical" href="{link application='cms' controller='Index'}{/link}" />
</head>

<body id="tpl{$templateName|ucfirst}">

{include file='header'}

<header class="boxHeadline">
    <h1>{PAGE_TITLE|language}</h1>
</header>

{@$layout}

{include file='footer' sandbox=false}
</body>
</html>