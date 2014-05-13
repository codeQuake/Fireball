<header {if $data['type'] == 'h1'} class="boxHeadline"{elseif $data['type'] == h2} class="boxSubHeadline"{elseif $data['type'] == h3} class="containerHeadline"{/if}>
	{if $data['hyperlink']|isset && $data['hyperlink'] != ""}<a href="{$data['hyperlink']}">{/if}<{$data['type']}>{$data['text']|language}</{$data['type']}>{if $data['hyperlink']|isset && $data['hyperlink'] != ""}</a>{/if}
</header>
