<div class="tabMenuContainer">
	<nav class="tabMenu">
		<ul>
			<li><a href="{@$__wcf->getAnchor('folder0')}">{lang}cms.acp.file.folderID.root{/lang}</a></li>
			{foreach from=$folders item=folder}
			{capture assign='anchor'}folder{$folder->folderID}{/capture}

			<li><a href="{@$__wcf->getAnchor($anchor)}">{$folder->getTitle()}</a></li>
			{/foreach}
			</ul>
	</nav>
	<div class="tabMenuContent containerPadding container imageSelect" id="folder0">

		<div class="container">
			<ol class="containerList doubleColumned">
			{foreach from=$images item=image}
				<li class="jsFileImage" data-object-id="{$image->fileID}">
					<div class="box32 pointer">
						<div class="framed">
							<img src="{$image->getURL()}" alt="" style="max-width: 32px; max-height: 32px;" />
						</div>
						<div>
							<p>{$image->title|truncate:15}</p>
						</div>
					</div>
				</li>
			{/foreach}
			</ol>
		</div>
	</div>
	{foreach from=$folders item=folder}
<div class="tabMenuContent containerPadding container imageSelect" id="folder{$folder->folderID}">

		<div class="container">
			<ol class="containerList doubleColumned">
			{foreach from=$folder->getFiles('image') item=image}
				<li class="jsFileImage" data-object-id="{$image->fileID}">
					<div class="box32 pointer">
						<div class="framed">
							<img src="{$image->getURL()}" alt="" style="max-width: 32px; max-height: 32px;" />
						</div>
						<div>
							<p>{$image->title|truncate:15}</p>
						</div>
					</div>
				</li>
			{/foreach}
			</ol>
		</div>
	</div>
{/foreach}
</div>



<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.TabMenu.init();
		});
	//]]>
</script>
