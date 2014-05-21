{hascontent}
	<ul class="sidebarBoxList">
		{content}
			{foreach from=$mostReadNews item=news}
				<li>
					<div class="sidebarBoxHeadline">
						<h3><a href="{link application='cms' controller='News' object=$news->getDecoratedObject()}{/link}" class="newsLink" data-news-id="{@$news->newsID}" data-sort-order="DESC" title="{$news->subject}">{$news->subject}</a></h3>
						<small>{$news->clicks} {lang}cms.news.clicks{/lang}</small>
					</div>
				</li>
			{/foreach}
		{/content}
	</ul>
{/hascontent}
