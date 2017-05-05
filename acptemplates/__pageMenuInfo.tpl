{js application='cms' file='Fireball.ACP' acp='true'}

<script data-relocate="true">
	require(['Language'], function(Language) {
		Language.addObject({
			'wcf.acp.pageMenu.parameters.notice': '{lang}wcf.acp.pageMenu.parameters.notice{/lang}'
		});

		new Fireball.ACP.Page.Menu();
	});
</script>
