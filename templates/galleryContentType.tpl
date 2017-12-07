<div class="galleryContainer center">
	{foreach from=$images item=image}
		<div class="galleryImage">
			<a class="embeddedAttachmentLink jsImageViewer" href="{$image->getLink()}"><img src="{if $content->useThumbnail}{$image->getThumbnailLink()}{else}{$image->getLink()}{/if}" data-hq-url="{$image->getLink()}" alt="{$image->title}"{if $content->text} title="{$content->text|language}"{/if} /></a>
		</div>
	{/foreach}
</div>
