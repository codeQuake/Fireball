<header {if $data['type'] == 'h1'} class="boxHeadline"{elseif $data['type'] == h2} class="boxSubHeadline"{elseif $data['type'] == h3} class="containerHeadline"{/if}>
	<{$data['type']}>{$data['text']|language}</{$data['type']}>
</header>
