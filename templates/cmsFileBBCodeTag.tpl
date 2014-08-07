{if $_file|isset}
	{if $_isImage}
		<figure {if $_align != ''}style="float: {$_align}; margin-top: 7px; margin-bottom: 7px; {if $_align == 'left'}margin-right: 14px;{else}margin-left: 14px;{/if}"{/if}>
			<img class="jsResizeImage" src="{$_file->getURL()}" alt="" {if $_width != 0}style="width: {$_width}px;"{/if} />
			<figcaption>{if $_caption != ''}<small class="center">{$_caption}</small>{/if}</figcaption>
		</figure>
	{else}
		<a href="{$_file->getURL()}">{$_file->getTitle()}</a>
	{/if}
{/if}
