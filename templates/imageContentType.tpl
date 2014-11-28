<figure>
	{if $data['link']|isset && $data['link'] != ''}<a href="{$data['link']}" class="framed">{/if}
	<img src="{$image->getLink()}" alt="{$image->title}" title="{if $data['text']|isset}{$data['text']|language}{/if}" class="jsTooltip" {if $data['width']|isset && $data['height']|isset}style="width: {$data['width']}px;"{/if}/>
	{if $data['link']|isset && $data['link'] != ''}</a>{/if}

	{if $data['text']|isset}
		<figcaption class="caption">{$data['text']|language}{if $data['link']|isset && $data['link'] != ''}<a href="{$data['link']}">{/if}{if $data['link']|isset && $data['link'] != ''}</a>{/if}</figcaption>
	{/if}
</figure>
