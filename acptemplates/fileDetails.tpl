<div>
	<section class="section">
		<h2 class="sectionTitle">{@$file->getIconTag()} <a href="{$file->getLink()}">{$file->getTitle()}</a></h2>

		{if $file->fileType == 'image/png' || $file->fileType == 'image/jpeg' || $file->fileType == 'image/gif'}
			<figure class="framed">
				<img style="max-width: 300px" src="{$file->getLink()}" alt="" />
				<figcaption><small>{$file->filesize|filesize} | {$file->fileType}</small></figcaption>
			</figure>
		{else}
			<small>{$file->filesize|filesize} | {$file->fileType}</small>
		{/if}
	</section>

	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.message.share{/lang}</h2>

		<input type="text" readonly="readonly" class="long" value="[cmsfile={@$file->fileID}][/cmsfile]" />
		<input type="text" readonly="readonly" class="long" value="{$file->getLink()}" style="margin-top: 9px" />
	</section>
</div>
