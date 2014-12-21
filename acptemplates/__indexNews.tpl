<div id="codequake">
	<fieldset>
		<legend>codeQuake</legend>

		{hascontent}
			<ul>
				{content}
					{if $codequakeNewsFeed|isset}
						{foreach from=$codequakeNewsFeed item=item}
							<li style="border-bottom: 1px dashed #dfdfdf; padding: 5px; margin-bottom: 5px;">
								<div class="containerHeadline">
									<h3><a href="{$item['link']}">{$item['title']}</a></h3>
									<small>{@$item['time']|time}</small>
								</div>
								<div>
									{@$item['description']}
								</div>
							</li>
						{/foreach}
					{/if}
				{/content}
			</ul>
		{/hascontent}
	</fieldset>
</div>
