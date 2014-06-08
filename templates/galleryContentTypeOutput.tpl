<div class="slideshowContainer">
	<ul>
		{foreach from=$images item=image}
			<li><img src="{$image->getURL()}" alt="" class="jsResizeImage" /></li>
		{/foreach}
	</ul>
</div>

<script data-relocate="true">
        //<![CDATA[
        $(function() {
        $('.slideshowContainer').wcfSlideshow();
        });
        //]]>
 </script>
