{if $position|empty}{assign var=position value="content"}{/if}

{assign var=oldDepth value=0}
{foreach from=$contentNodeTree item=content}
	{if $content->getTypeName() != 'de.codequake.cms.content.type.box'}
		{section name=i loop=$oldDepth-$contentNodeTree->getDepth()}</section>{/section}
		<section class="{if $position == 'sidebar'}box {/if}{if $content->getCSSClasses()}{$content->getCSSClasses()}{/if}" id="cmsContent{@$content->contentID}" data-content-type="{$content->getTypeName()}">
			{if $content->showHeadline}<h2 class="boxTitle">{$content->getTitle()}</h2>{/if}

			{if $position == 'sidebar'}<div class="boxContent">{/if}
				{@$content->getOutput()|language}
				{if !$contentNodeTree->current()->hasChildren()}
					{if $position == 'sidebar'}</div>{/if}</section>
				{/if}

			{assign var=oldDepth value=$contentNodeTree->getDepth()}
	{else}
		{@$content->getOutput()|language}
	{/if}
{/foreach}
{section name=i loop=$oldDepth}</div></section>{/section}
