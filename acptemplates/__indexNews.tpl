<div id="codequake">
	<ul class="containerList recentActivityList">
		{if $codequakeNewsFeed|isset}
			{foreach from=$codequakeNewsFeed item=item}
				<li>
					<div class="containerHeadline">
						<h3>
							<a href="{@$__wcf->getPath()}acp/dereferrer.php?url={$item['link']|rawurlencode}">{$item['title']}</a>
							<small>{@$item['time']|time}</small>
						</h3>
					</div>
					<div>
						{@$item['description']}
					</div>
				</li>
			{/foreach}
		{/if}
		<li class="recentActivitiesMore showMore"><a href="{@$__wcf->getPath()}acp/dereferrer.php?url={'http://codequake.de/index.php/NewsCategoryList/'|rawurlencode}" class="button small">{lang}cms.acp.index.news.more{/lang}</a></li>
	</ul>
</div>
