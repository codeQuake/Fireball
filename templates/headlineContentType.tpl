<header {if $content->type == 'h1'} class="boxHeadline"{elseif $content->type == h2} class="boxSubHeadline"{elseif $content->type == h3} class="containerHeadline"{/if}>
	{if $content->link}<a href="{$content->link}">{/if}<{$content->type}>{$content->text|language}</{$content->type}>{if $content->link}</a>{/if}
</header>
