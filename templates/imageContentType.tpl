<a href="{if $content->link}{$content->link}{else}{$image->getLink()}{/if}" class="framed embeddedAttachmentLink{if !$content->link} jsImageViewer{/if}">
	<img src="{if $image->useThumbnail}{$image->getThumbnailLink()}{else}{$image->getLink()}{/if}" data-hq-url="{$image->getLink()}" alt="{$image->title}"{if $content->text} title="{$content->text|language}"{/if}{if $content->width} style="width: {$content->width}px"{/if} />
	{if $content->text}<br><span class="dimmed caption">{$content->text|language}</span>{/if}
</a>
