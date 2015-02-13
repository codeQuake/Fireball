{hascontent}
  <div class="rssFeedContainer">
    <ul>
      {content}
        {if $rssFeed|isset}
          {foreach from=$rssFeed item=item}
            <li class="rssItemContainer">
              <div class="rssItemContainerHeadline containerHeadline">
                <h3>
                  <a href="{$item['link']}">{$item['title']}</a>
                </h3>
                <small>
                  <span class="usename">
                     {$item['author']}
                  </span> 
                  ({@$item['time']|time})
                </small>
              </div>
              <div class="rssItemContainerContent">
                {@$item['description']}
              </div>
            </li>
          {/foreach}
        {/if}
      {/content}
    </ul>
  </div>
{/hascontent}