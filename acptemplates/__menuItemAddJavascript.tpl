<script data-relocate="true">
	$(function() {
		{if $action == 'edit'}handleFireballPage();{/if}
		$('#pageID').change(handleFireballPage);
		function handleFireballPage () {
			var identifier = $('#pageID option:selected').data('identifier');
			var match = identifier.match('^(de.codequake.cms.page)([0-9]+)');
			if (match) {
				$('#pageObjectIDContainer').hide();
				$('#pageObjectID').val(match[2]);
			}
		}
	});
</script>
