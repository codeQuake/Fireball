{include file='header'}

<section class="section">
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
</section>

{include file='footer'}
