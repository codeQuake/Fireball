<a class="{if $content->type == 'button'}button{else if $content->type == 'smallbutton'}button small{/if}" href="{$content->link|language}">
	{$content->text|language}
</a>
