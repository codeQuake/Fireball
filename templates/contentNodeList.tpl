{if $position|empty}{assign var=position value='content'}{/if}

{assign var=oldDepth value=0}
{foreach from=$contentNodeTree item=content}
	{section name=i loop=$oldDepth-$contentNodeTree->getDepth()}{if $position == 'sidebar'}</section>{else}</div>{/if}{/section}
	{if $content->getTypeName() != 'de.codequake.cms.content.type.box'}
		{if $position == 'sidebar'}
			<section class="box{if $content->getCSSClasses()} {$content->getCSSClasses()}{/if}" id="cmsContent{@$content->contentID}" data-content-type="{$content->getTypeName()}">
		{else}
			<div{if $content->getCSSClasses()} class="{$content->getCSSClasses()}"{/if} id="cmsContent{@$content->contentID}" data-content-type="{$content->getTypeName()}">
		{/if}
		{if $content->showHeadline}<h2 class="sectionTitle{if $position == 'sidebar'} boxTitle{/if}">{$content->getTitle()}</h2>{/if}

		{if $position == 'sidebar'}<div class="boxContent">{/if}
		{@$content->getOutput()|language}
		{if !$contentNodeTree->current()->hasChildren()}
			{if $position == 'sidebar'}</div></section>{else}</div>{/if}
		{/if}

		{assign var=oldDepth value=$contentNodeTree->getDepth()}
	{else}
		{@$content->getOutput()|language}
	{/if}
{/foreach}
{section name=i loop=$oldDepth}{if $position == 'sidebar'}</div></section>{else}</div>{/if}{/section}
