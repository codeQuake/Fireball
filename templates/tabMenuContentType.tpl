<nav class="tabMenu">
	<ul>
		{foreach from=$children item=child}
			<li><a href="{@$__wcf->getAnchor('cmsContent'|concat:$child->contentID)}">{$child->getTitle()}</a></li>
		{/foreach}

		{event name='tabMenuTabs'}
	</ul>
</nav>

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.TabMenu.init();
	});
	//]]>
</script>
