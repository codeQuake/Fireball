{if $__wcf->getSession()->getPermission('admin.fireball.page.canAddPage')}
<li id="pageAddButton">
	<a href="{link controller='PageAdd'}{/link}">
		<span class="icon icon16 fa-plus-square"></span>
		<span>{lang}cms.acp.page.add{/lang}</span>
	</a>
	
	<script data-relocate="true" src="{@$__wcf->getPath('cms')}js/CMS{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new Fireball.Page.Add({if $page|isset && $page != null}{$page->pageID}{/if})
		});
		//]]>
	</script>
</li>
{/if}