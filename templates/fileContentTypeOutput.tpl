<div class="box32">
	{@$file->getIconTag(32)}
	<div class="details">
		<a href="{@$__wcf->getPath('cms')}{$file->getURL()}" type="{$file->type}">{$file->getTitle()}</a>
		<p>
			<small>{$file->size|filesize}</small>
		</p>
	</div>
</div>
