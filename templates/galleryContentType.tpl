<div class="galleryContainer center">
	{foreach from=$images item=image}
		<div class="galleryImage">
			<a class="embeddedAttachmentLink jsImageViewer" href="{$image->getLink()}"><img src="{$image->getThumbnailLink()}" alt="" /></a>
		</div>
	{/foreach}
</div>
