<input type="checkbox" id="contentAdd" />
<label for="contentAdd" id="contentAddLabel">
	<span class="icon icon16 icon-chevron-right"></span>
</label>
<div id="contentRibbon">
	<div class="containerPadding">
		{foreach from=$contentTypes key=category item=types}
		<h2>{lang}cms.acp.content.type.{$category}{/lang}</h2>
		<div>
			<ul class="containerBoxList contentTypeList">
				{foreach from=$types item=type}
				<li id="{$type->objectType}" class="draggable box24">
					<span class="icon icon24 {$type->getProcessor()->getIcon()}"></span>
					<h3 class="boxHeadline">{lang}cms.acp.content.type.{$type->objectType}{/lang}</h3>
				</li>
				{/foreach}
			</ul>
		</div>
		{/foreach}
	</div>
</div>