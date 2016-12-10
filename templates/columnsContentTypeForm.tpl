<div id="columnContainer" class="gridContainer"></div>
<div class="contentNavigation">
	<nav>
		<ul>
			<li><button type="button" class="jsAddColumn">{lang}cms.acp.content.type.de.codequake.cms.content.type.columns.column.add{/lang}</button></li>
		</ul>
	</nav>
</div>
<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/Fireball.ACP{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
	
<script data-relocate="true">
	$(function() {
		new Fireball.ACP.Content.Type['de.codequake.cms.content.type.columns']([{if $contentData['columnData']|isset}{implode from=$contentData['columnData'] item=column}{@$column}{/implode}{/if}]);
	});
</script>
