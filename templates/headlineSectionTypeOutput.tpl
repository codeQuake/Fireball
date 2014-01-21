{if $hyperlink != ''}<{$hlType}><a href="{$hyperlink}">{$sectionData|language}</a></{$hlType}>
{else}
<{$hlType}>{$sectionData|language}</{$hlType}>
{/if}