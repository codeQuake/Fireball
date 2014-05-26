{if $type == 'standard'}
{include file='newsListing' application='cms'}
{else if $type == 'boxed'}
{foreach from=$objects item=news}
<div class="box128">
	<div class="newsImage">
		<a href="{link controller='News' object=$news application='cms'}{/link}"><img src="{if $news->imageID != 0}{$news->getImage()->getURL()}{else}{$news->getUserProfile()->getAvatar()->getURL()}{/if}" alt="{$news->getTitle()}" style="max-width: 120px;" /></a>
	</div>

	<div class="containerHeadline">
		<h3><a class="newsLink" data-news-id="{$news->newsID}" href="{link controller='News' object=$news application='cms'}{/link}">{$news->getTitle()}</a></h3>
		<small>
            <span class="username">
                <a class="userLink" data-user-id="{$news->userID}" href="{link controller='User' object=$news->getUserProfile()}{/link}">
                    {$news->username}
                </a>
            </span>
            -
            <a class="permalink" href="{link controller='News' object=$news application='cms'}{/link}">
                {@$news->time|time}
            </a>
			{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike') && $news->likes || $news->dislikes}<span class="likesBadge badge jsTooltip {if $news->cumulativeLikes > 0}green{elseif $news->cumulativeLikes < 0}red{/if}" title="{lang likes=$news->likes dislikes=$news->dislikes}wcf.like.tooltip{/lang}">{if $news->cumulativeLikes > 0}+{elseif $news->cumulativeLikes == 0}&plusmn;{/if}{#$news->cumulativeLikes}</span>{/if}
        </small>
        <p>
			<strong>{if $news->teaser != ""}{$news->teaser}{else}{@$news->getExcerpt()}{/if}</strong>
		</p>
	</div>
</div>
{/foreach}
{else if $type == 'simple1'}
{foreach from=$objects item=news}
<div class="simpleNews">
	<div class="containerHeadline">
		<h3><a class="newsLink" data-news-id="{$news->newsID}" href="{link controller='News' object=$news application='cms'}{/link}">{$news->getTitle()}</a></h3>
		<p>{if $news->teaser != ""}{$news->teaser}{else}{@$news->getExcerpt()}{/if}</p>
	</div>
</div>
{/foreach}
{else if $type == 'simple2'}
{foreach from=$objects item=news}
<div class="box48 simpleNews">
	<div class="newsImage">
		<a href="{link controller='News' object=$news application='cms'}{/link}"><img src="{if $news->imageID != 0}{$news->getImage()->getURL()}{else}{$news->getUserProfile()->getAvatar()->getURL()}{/if}" alt="{$news->getTitle()}" style="max-width: 40px;" /></a>
	</div>
	<div class="containerHeadline">
		<h3><a class="newsLink" data-news-id="{$news->newsID}" href="{link controller='News' object=$news application='cms'}{/link}">{$news->getTitle()}</a></h3>
		<small>
            <span class="username">
                <a class="userLink" data-user-id="{$news->userID}" href="{link controller='User' object=$news->getUserProfile()}{/link}">
                    {$news->username}
                </a>
            </span>
            -
            <a class="permalink" href="{link controller='News' object=$news application='cms'}{/link}">
                {@$news->time|time}
            </a>
			{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike') && $news->likes || $news->dislikes}<span class="likesBadge badge jsTooltip {if $news->cumulativeLikes > 0}green{elseif $news->cumulativeLikes < 0}red{/if}" title="{lang likes=$news->likes dislikes=$news->dislikes}wcf.like.tooltip{/lang}">{if $news->cumulativeLikes > 0}+{elseif $news->cumulativeLikes == 0}&plusmn;{/if}{#$news->cumulativeLikes}</span>{/if}
        </small>
	</div>
</div>
{/foreach}
{/if}
