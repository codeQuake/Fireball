{if $_file|isset}
	{if $_isImage}
		<figure {if $_align != ''}style="float: {$_align};"{/if}>
			<img class="jsResizeImage" src="{$_file->getURL()}" alt="" {if $_width != 0}style="width: {$_width}px;"{/if} />
			<figcaption>{if $_caption != ''}<small class="center">{$_caption}</small>{/if}</figcaption>
		</figure>
	{else}
		<a href="{$_file->getURL()}">{$_file->getTitle()}</a>
	{/if}
{/if}
