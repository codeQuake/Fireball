<ul class="sitemapList">
	{assign var=oldDepth value=0}
	{foreach from=$pageList item=page}
		{section name=i loop=$oldDepth-$pageList->getDepth()}</ul></li>{/section}
		<li>
			<a href="{$page->getLink()}">{$page->getTitle()}</a>
			<ul>
				{if !$pageList->current()->hasChildren()}</ul></li>{/if}
				{assign var=oldDepth value=$pageList->getDepth()}
		</li>
	{/foreach}
	{section name=i loop=$oldDepth}</ul></li>{/section}
</ul>
