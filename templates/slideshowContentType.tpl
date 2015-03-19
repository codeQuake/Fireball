<script data-relocate="true">
		//<![CDATA[
		$(window).load(function() {
			$("{'#cmsContent'|concat:$content->contentID}").fireSlide({
				speed: {CMS_CONTENT_SLIDESHOW_INTERVAL}
			});
		});
		//]]>
</script>