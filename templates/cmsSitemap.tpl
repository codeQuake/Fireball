<ul class="sitemapList" data-object-id="0">
    {foreach from=$pageList item=page}
    <li>
        <a href="{link controller='Page' application='cms' object=$page}{/link}">{$page->getTitle()|language}</a>
        {if $page->hasChildren()}
            <ul data-object-id="{$page->pageID}">
                {foreach from=$page->getChildren() item=child}
                    <li>
                        <a href="{link controller='Page' application='cms' object=$child}{/link}">{$child->getTitle()|language}</a>
                    </li>
                {/foreach}
            </ul>
        {/if}
    </li>
    {/foreach}
    </ul>