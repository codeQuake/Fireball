<div>
	<fieldset>
		<legend>{@$file->getIconTag()} <a href="{$file->getLink()}">{$file->getTitle()}</a></legend>

		{if $file->fileType == 'image/png' || $file->fileType == 'image/jpeg' || $file->fileType == 'image/gif'}
			<figure class="framed">
				<img style="max-width: 300px" src="{$file->getLink()}" alt="" />
				<figcaption><small>{$file->size|filesize} | {$file->fileType}</small></figcaption>
			</figure>
		{else}
			<small>{$file->size|filesize} | {$file->fileType}</small>
		{/if}
	</fieldset>

	<fieldset>
		<legend>{lang}wcf.message.share{/lang}</legend>

		<input type="text" readonly="readonly" class="long" value="[cmsfile={@$file->fileID}][/cmsfile]" />
		<input type="text" readonly="readonly" class="long" value="{$file->getLink()}" style="margin-top: 9px" />
	</fieldset>
</div>
