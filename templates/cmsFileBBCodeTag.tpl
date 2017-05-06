{if $_file|isset}
	{if $_isImage}
		<a href="{$_file->getLink()}" class="embeddedAttachmentLink jsImageViewer" {if $_align != ''}style="float: {$_align}; margin-top: 7px; margin-bottom: 7px; {if $_align == 'left'}margin-right: 14px;{else}margin-left: 14px;{/if}"{/if}>
			<img class="jsResizeImage" src="{$_file->getThumbnailLink()}" alt="" {if $_width != 0}style="width: {$_width}px;"{/if} />
			<span class="dimmed">{if $_caption != ''}<small class="center">{$_caption}</small>{/if}</span>
		</a>
	{else}
		<a href="{$_file->getLink()}">{$_file->getTitle()}</a>
	{/if}
{/if}
