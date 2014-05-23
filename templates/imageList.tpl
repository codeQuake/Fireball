<div class="tabMenuContainer">
	<nav class="tabMenu">
		<ul>
			<li><a href="{@$__wcf->getAnchor('imageUpload')}">{lang}wcf.global.button.upload{/lang}</a></li>
			<li><a href="{@$__wcf->getAnchor('imageSelect')}">{lang}cms.news.image.select{/lang}</a></li>
		</ul>
	</nav>

	<div class="tabMenuContent containerPadding container imageUpload" id="imageUpload">
		<ul>
			{if $image|isset}
			<li class="box32">
				<div class="framed">
					<img src="{$image->getURL()}" alt="{$image->title}" class="newsImage" style="max-width: 32px; max-height: 32px;" />
				</div>
				<div>
					<p>{$image->title}</p>
				</div>
			</li>
			{/if}
		</ul>
		<div id="imageUploadButton"></div>
	</div>
	<div class="tabMenuContent containerPadding container imageSelect" id="imageSelect">
		<div class="container">
			<ol class="containerList doubleColumned">
			{foreach from=$images item=image}
				<li class="jsNewsImage" data-object-id="{$image->imageID}">
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
</div>

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.TabMenu.init();
		});
	//]]>
</script>
