<div id="codequake">
	<ul class="containerList recentActivityList">
		{if $codequakeNewsFeed|isset}
			{foreach from=$codequakeNewsFeed item=item name='codequakeNews'}
				<li{if $tpl.foreach.codequakeNews.first} style="border-top-left-radius: 0; border-top-right-radius: 0"{/if}>
					<div class="containerHeadline">
						<h3>
							<a href="{$item['link']}">{$item['title']}</a>
							<small>{@$item['time']|time}</small>
						</h3>
					</div>
					<div>
						{@$item['description']}
					</div>
				</li>
			{/foreach}
		{/if}
		<li class="recentActivitiesMore showMore"><a href="http://codequake.de/index.php/NewsCategoryList/" class="button small">{lang}cms.acp.index.news.more{/lang}</a></li>
	</ul>
</div>
