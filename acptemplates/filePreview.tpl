<div class="box96">  
	<div>
		{if $file->isImage()}
			<p class="framed"><img src="{$file->getLink()}" style="max-width: 96px" alt="" /></p>
		{else}
			<p class="framed">{@$file->getIconTag(96)}</p>
		{/if}
	</div>
	<div class="containerHeadline">
		<h3>{$file->getTitle()}</h3>
		<small>{$file->fileSize|filesize} - {$file->fileType} - {@$file->uploadTime|time}</small>
	</div>
</div>
