{if $objects|count}

		{foreach from=$objects item=news}
			
                <a data-news-id="{@$news->newsID}" class=" messageGroupLink framed" href="{link application='cms' controller='News' object=$news}{/link}">{$news->subject}</a>
              
            <div class="box24">{@$news->getExcerpt()}</div>
          
		{/foreach}
{/if}