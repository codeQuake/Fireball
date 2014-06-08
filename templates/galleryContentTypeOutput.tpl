<div class="slideshowContainer">
	<ul>
		{foreach from=$images item=image}
			<li><img src="{$image->getURL()}" alt="" class="jsResizeImage" /></li>
		{/foreach}
	</ul>
</div>
