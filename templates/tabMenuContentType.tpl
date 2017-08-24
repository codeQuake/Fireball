<nav class="tabMenu">
	<ul>
		{foreach from=$children item=child}
			{assign var='anchorName' value='cmsContent'|concat:$child->contentID}
			<li><a href="{@$__wcf->getAnchor($anchorName)}">{$child->getTitle()}</a></li>
		{/foreach}

		{event name='tabMenuTabs'}
	</ul>
</nav>
