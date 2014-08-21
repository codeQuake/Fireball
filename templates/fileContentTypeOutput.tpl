<div class="box32">
	{@$file->getIconTag(32)}
	<div class="details">
		<a href="{link controller='FileDownload' application='cms' object=$file}{/link}" type="{$file->type}">{$file->getTitle()}</a>
		<p>
			<small>{$file->size|filesize}</small>
		</p>
	</div>
</div>
