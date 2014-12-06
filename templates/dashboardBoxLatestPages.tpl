<ul class="sidebarBoxList">
	{foreach from=$pageList item=item}
		<li>
			<div class="sidebarBoxHeadline">
				<h3><a href="{$item->getLink()}">{$item->getTitle()}</a></h3>
				<small>{@$item->creationTime|time}</small>
			</div>
		</li>
	{/foreach}
</ul>
