{if $position|empty}{assign var=position value='content'}{/if}

{assign var=oldDepth value=0}
{foreach from=$contentNodeTree item=content}
	{section name=i loop=$oldDepth-$contentNodeTree->getDepth()}{if $position == 'sidebar'}</fieldset>{else}</div>{/if}{/section}
	{if $content->getTypeName() != 'de.codequake.cms.content.type.box'}
		{if $position == 'sidebar'}
		<fieldset class="dashboardBox{if $content->getCSSClasses()} {$content->getCSSClasses()}{/if}" id="cmsContent{@$content->contentID}" data-content-type="{$content->getTypeName()}">
		{else}
			<div{if $content->getCSSClasses()} class="{$content->getCSSClasses()}"{/if} id="cmsContent{@$content->contentID}" data-content-type="{$content->getTypeName()}">
		{/if}
		{if $content->showHeadline}
			{if $position == 'sidebar'}
				<legend>{$content->getTitle()}</legend>
			{else}
				<h2 class="boxTitle">{$content->getTitle()}</h2>
			{/if}
		{/if}

		{if $position == 'sidebar'}<div class="boxContent">{/if}
		{@$content->getOutput()|language}
		{if !$contentNodeTree->current()->hasChildren()}
			{if $position == 'sidebar'}</div></fieldset>{else}</div>{/if}
		{/if}

		{assign var=oldDepth value=$contentNodeTree->getDepth()}
	{else}
		{@$content->getOutput()|language}
	{/if}
{/foreach}
{section name=i loop=$oldDepth}{if $position == 'sidebar'}</div></fieldset>{else}</div>{/if}{/section}
