<ul class="cmsMenu">
	{assign var=oldDepth value=0}
	{foreach from=$menuItems item=item}
	{section name=i loop=$oldDepth-$menuItems->getDepth()}</ul></li>{/section}
	<li>
		<a href="{$item->getLink()}">{$item->getTitle()}</a>
		<ul>
		{if !$menuItems->current()->hasChildren()}
		</ul>
		</li>
		{/if}
		{assign var=oldDepth value=$menuItems->getDepth()}
	</li>
	{/foreach}
	{section name=i loop=$oldDepth}</ul></li>{/section}
</ul>

