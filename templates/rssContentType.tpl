{hascontent}
	<div class="rssFeedContainer">
		<ul class="articleList">
			{content}
			{if $rssFeed|isset}
				{foreach from=$rssFeed item=item}
					<li class="rssItemContainer">
						<a href="{$item['link']}"><a href="{$item['link']}">
								<div>
									<div class="containerHeadline">
										<h3 class="articleListTitle">{$item['title']}</h3>

										<ul class="inlineList articleListMetaData">
											<li>
												<span class="icon icon16 fa-clock-o"></span>
												{@$item['time']|time}
											</li>

											<li>
												<span class="icon icon16 fa-user"></span>
												{$item['author']}
											</li>
									</div>

									<div class="containerContent articleListTeaser">
										{@$item['description']}
									</div>
								</div>
							</a>
					</li>
				{/foreach}
			{/if}
			{/content}
		</ul>
	</div>
{/hascontent}
