<script data-relocate="true">
	$(window).on('load', function() {
		$("{'#cmsContent'|concat:$content->contentID}").fireSlide({
			speed: {FIREBALL_CONTENT_SLIDESHOW_INTERVAL}
		});
	});
</script>
