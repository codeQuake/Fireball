<div id="columnContainer" class="gridContainer"></div>
<div class="contentNavigation">
	<nav>
		<ul>
			<li><button type="button" class="jsAddColumn">{lang}cms.acp.content.type.de.codequake.cms.content.type.columns.column.add{/lang}</button></li>
		</ul>
	</nav>
</div>

<script data-relocate="true">
	$(function() {
		new Fireball.ACP.Content.Type['de.codequake.cms.content.type.columns']([{if $contentData['columnData']|isset}{implode from=$contentData['columnData'] item=column}{@$column}{/implode}{/if}]);
	});
</script>
