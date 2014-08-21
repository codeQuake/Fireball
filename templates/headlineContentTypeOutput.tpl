<header {if $data['type'] == 'h1'} class="boxHeadline"{elseif $data['type'] == h2} class="boxSubHeadline"{elseif $data['type'] == h3} class="containerHeadline"{/if}>
	{if $data['link']|isset && $data['link'] != ""}<a href="{$data['link']}">{/if}<{$data['type']}>{$data['text']|language}</{$data['type']}>{if $data['link']|isset && $data['link'] != ""}</a>{/if}
</header>
