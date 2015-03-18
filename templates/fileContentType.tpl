<div class="box32">
	{@$file->getIconTag(32)}
	<div class="details">
		<a href="{link controller='FileDownload' application='cms' object=$file}{/link}" type="{$file->fileType}">{$file->getTitle()}</a>
		<p>
			<small>{$file->filesize|filesize}</small>
		</p>
	</div>
</div>
