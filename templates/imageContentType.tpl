<figure>
	{if $content->link}<a href="{$content->link}" class="framed">{/if}
	<img src="{$image->getLink()}" alt="{$image->title}"{if $content->text} title="{$content->text|language}"{/if} class="jsTooltip"{if $content->width && $content->height} style="width: {$content->width}px"{/if} />
	{if $content->link}</a>{/if}

	{if $content->text}
		<figcaption class="caption">{if $content->link}<a href="{$content->link}">{/if}{$content->text|language}{if $content->link}</a>{/if}</figcaption>
	{/if}
</figure>
