<figure>
{if $data['link']|isset && $data['link'] != ''}<a href="{$data['link']}" class="framed">{/if}
	<img src="{$image->getURL()}" alt="{$image->title}" title="{if $data['text']|isset}{$data['text']}{/if}" class="jsTooltip jsResizeImage"/>
{if $data['link']|isset && $data['link'] != ''}</a>{/if}
{if $data['text']|isset}<figcaption class="caption">{$data['text']}</figcaption>{/if}
</figure>
