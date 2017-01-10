<div class="gridContainer galleryContainer center">
	{foreach from=$images item=image}
		<div class="grid grid25 galleryImage shadow marginTop">
			<figure>
				<a class="imgThumb jsImageViewer" href="{$image->getLink()}"><img src="{$image->getThumbnailLink()}" alt="" /></a>
			</figure>
		</div>
	{/foreach}
</div>
