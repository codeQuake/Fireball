<div class="containerPadding">
	{foreach from=$contentTypes key=category item=types}
	<header>
		<h2>
			<a class="jsCollapsible" data-is-open="0" data-collapsible-container="{$category}"><span class="icon icon16 icon-chevron-right"></span></a>
			{lang}cms.acp.content.type.{$category}{/lang}
		<h2>
	</header>
	<div id="{$category}">
		<ul class="containerBoxList contentTypeList">
			{foreach from=$types item=type}
			<li id="{$type->objectType}" class="draggable container black">
				<h3 class="boxHeadline">
					<span class="icon icon24 {$type->getProcessor()->getIcon()}"></span>
					{lang}cms.acp.content.type.{$type->objectType}{/lang}
				</h3>
			</li>
			{/foreach}
		</ul>
	</div>
	{/foreach}
</div>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.Collapsible.Simple.init();
	});
	//]]>
</script>