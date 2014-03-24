<ul class="sitemapList" data-object-id="0">
    {foreach from=$pageList item=page}
    <li>
        <a href="{$page->getLink()}">{$page->getTitle()|language}</a>
        {if $page->hasChildren()}
            <ul data-object-id="{$page->pageID}">
                {foreach from=$page->getChildren() item=child}
                    <li>
                        <a href="{$child->getLink()}">{$child->getTitle()|language}</a>
                    </li>
                {/foreach}
            </ul>
        {/if}
    </li>
    {/foreach}
    </ul>