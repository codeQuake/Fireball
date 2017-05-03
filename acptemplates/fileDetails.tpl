<section class="section">
	<h2 class="sectionTitle">{@$file->getIconTag()} <a href="{$file->getLink()}">{$file->getTitle()}</a></h2>

	{if $file->isImage()}
		<figure class="framed">
			<img style="max-width: 300px" src="{$file->getThumbnailLink()}" alt="" />
			<figcaption>
				<ul class="inlineList dotSeparated">
					<li>{$file->filesize|filesize}</li>
					<li>{$file->fileType}</li>
					{event name='fileDetailList'}
				</ul>
			</figcaption>
		</figure>
	{else}
		<ul class="inlineList dotSeparated">
			<li>{$file->filesize|filesize}</li>
			<li>{$file->fileType}</li>
			{event name='fileDetailList'}
		</ul>
	{/if}
</section>

<section class="section">
	<h2 class="sectionTitle">{lang}wcf.message.share{/lang}</h2>

	<input type="text" readonly class="long" value="[cmsfile={@$file->fileID}][/cmsfile]" onclick="this.select();" />
	<input type="text" readonly class="long" value="{$file->getLink()}" style="margin-top: 9px" onclick="this.select();" />
</section>

<section class="section">
	<h2 class="sectionTitle">{lang}cms.acp.file.data{/lang}</h2>

	<dl>
		<dt><label for="title">{lang}cms.acp.file.title{/lang}</label></dt>
		<dd>
			<input type="text" name="title" id="title" class="long" value="{$file->getTitle()}" />
		</dd>
	</dl>

	<dl>
		<dt><label for="categoryID">{lang}cms.acp.file.categoryIDs{/lang}</label></dt>
		<dd>
			<select id="categoryIDs" name="categoryIDs" multiple="multiple" class="long" size="10">
				{foreach from=$availableCategoryNodeList item=node}
					<option value="{@$node->categoryID}"{if $node->categoryID|in_array:$categoryIDs} selected{/if}>{@"&nbsp;&nbsp;&nbsp;&nbsp;"|str_repeat:$availableCategoryNodeList->getDepth()}{$node->getTitle()}</option>
				{/foreach}
			</select>
		</dd>
	</dl>
</section>

<!--
<section class="section">
	<h2 class="sectionTitle">{lang}cms.acp.file.userPermissions{/lang}</h2>

	<dl id="filePermissionsContainer">
		<dt>{lang}wcf.acl.permissions{/lang}</dt>
		<dd></dd>
	</dl>

	{event name='permissionFields'}
</section>
-->

<div class="formSubmit">
	<input class="fileEditSubmit" type="submit" value="{lang}wcf.global.button.submit{/lang}" />
	{@SECURITY_TOKEN_INPUT_TAG}
</div>
