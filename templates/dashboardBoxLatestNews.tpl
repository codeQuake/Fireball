{hascontent}
	<ul class="sidebarBoxList">
		{content}
			{foreach from=$latestNws item=news}
				<li class="box24">
					<a href="{link application='cms' controller='News' object=$news->getDecoratedObject()}{/link}" class="framed">{@$news->getUserProfile()->getAvatar()->getImageTag(24)}</a>
					
					<div class="sidebarBoxHeadline">
						<h3><a href="{link application='cms' controller='News' object=$news->getDecoratedObject()}{/link}" class="newsLink" data-news-id="{@$news->newsID}" data-sort-order="DESC" title="{$news->subject}">{$news->subject}</a></h3>
						<small>{if $news->userID}<a href="{link controller='User' object=$news->getUserProfile()->getDecoratedObject()}{/link}" class="userLink" data-user-id="{@$news->getUserProfile()->userID}">{$news->username}</a>{else}{$news->username}{/if} - {@$news->time|time}</small>
					</div>
				</li>
			{/foreach}
		{/content}
	</ul>
{/hascontent}