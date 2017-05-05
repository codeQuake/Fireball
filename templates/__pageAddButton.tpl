{if $__wcf->getSession()->getPermission('admin.fireball.page.canAddPage')}
	<li id="pageAddButton">
		<a href="{link controller='PageAdd'}{/link}">
			<span class="icon icon16 fa-plus-square"></span>
			<span>{lang}cms.acp.page.add{/lang}</span>
		</a>

		{js application='cms' file='Fireball'}
		<script data-relocate="true">
			$(function() {
				new Fireball.Page.Add({if $page|isset && $page != null}{$page->pageID}{/if})
			});
		</script>
	</li>
{/if}
