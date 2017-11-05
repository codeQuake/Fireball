<dl>
	<dt></dt>
	<dd>
		<label>
			<input type="checkbox" name="contentData[showInlineTitle]" id="contentData[showInlineTitle]" value="1"{if $contentData['showInlineTitle']|isset && $contentData['showInlineTitle']} checked{/if} />
			{lang}cms.acp.content.type.de.codequake.cms.content.type.graph.pie.showInlineTitle{/lang}
		</label>
	</dd>
</dl>
<dl>
	<dt></dt>
	<dd>
		<label>
			<input type="checkbox" name="contentData[showIn3D]" id="contentData[showIn3D]" value="1"{if $contentData['showIn3D']|isset && $contentData['showIn3D']} checked{/if} />
			{lang}cms.acp.content.type.de.codequake.cms.content.type.graph.pie.showIn3D{/lang}
		</label>
	</dd>
</dl>


<div class="section graphOptionContainer">
	<ul>
		<li class="left">
			{lang}cms.acp.content.type.de.codequake.cms.content.type.graph.pie.title{/lang}
		</li>
		<li class="right">
			{lang}cms.acp.content.type.de.codequake.cms.content.type.graph.pie.value{/lang}
		</li>
		<li class="iconList"></li>

		{if $contentData['graphOptions']|isset}
			{foreach from=$contentData[graphOptions][title] key=key item=title}
				{assign var=value value=$contentData[graphOptions][value][$key]}
				<li class="left" data-counter="{$key}">
					<input type="text" name="contentData[graphOptions][title][]" value="{$title}" class="long" required />
				</li>
				<li class="right" data-counter="{$key}">
					<input type="number" name=contentData[graphOptions][value][]" value="{$value}" class="tiny" required />
				</li>
				<li class="iconList" data-counter="{$key}">
					<span class="icon icon16 fa-remove pointer jsLineRemoveButton"></span>
				</li>
			{/foreach}
		{/if}
	</ul>
</div>

<div class="contentNavigation">
	<nav>
		<ul>
			<li><button type="button" class="jsAddGraphOption">{lang}cms.acp.content.type.de.codequake.cms.content.type.graph.option.add{/lang}</button></li>
		</ul>
	</nav>
</div>

<script data-relocate="true">
	$(function() {
		new Fireball.ACP.Content.Type['de.codequake.cms.content.type.graph.pie']();
	});
</script>

<style type="text/css">
	.graphOptionContainer > ul { display: flex; flex-flow: row wrap; }
	.graphOptionContainer > ul > li { padding: 5px; }
	.graphOptionContainer > ul > li.left { flex: 0 1 calc(100% - 120px - 10px); }
	.graphOptionContainer > ul > li.right { flex: 0 1 100px; }
	.graphOptionContainer > ul > li.iconList { flex: 0 1 20px; }
</style>
