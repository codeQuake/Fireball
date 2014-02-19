			<ol class="nestedCategoryList doubleColumned {if $errorField == 'sectionData'}formError{/if}">
				<script data-relocate="true">
                //<![CDATA[
                $(function () {
                    $("#folder0").click(function () {
                        $("#folderFiles0").toggle('slow');
                    });
                });
                //]]>
	        </script>
				<li>
					<div>
						<div class="containerHeadline">
							<h3><label id="folder0" title="{lang}cms.acp.folder.open{/lang}" class="jsTooltip"><span class="icon icon24 icon-folder-close"></span> {lang}cms.acp.file.folderID.root{/lang}</label></h3>
							
							<ol id="folderFiles0" style="display: none;">
							{foreach from=$fileList item=file}
								<li>
									<label><input type="checkbox" name="sectionData[]" value="{$file->fileID}" {if $file->fileID|in_array:$fileIDs}checked="checked" {/if}/>{@$file->getIconTag()} {$file->getTitle()}</label>
								</li>
							{/foreach}
							</ol>
						</div>
					</div>
				</li>
				{foreach from=$folderList item=folder}
				<script data-relocate="true">
                //<![CDATA[
                $(function () {
                    $("#folder{$folder->folderID}").click(function () {
                        $("#folderFiles{$folder->folderID}").toggle('slow');
                    });
                });
                //]]>
	        </script>
				<li>
					<div>
						<div class="containerHeadline">
							<h3><label id="folder{$folder->folderID}" title="{lang}cms.acp.folder.open{/lang}" class="jsTooltip"><span class="icon icon24 icon-folder-close"></span> {$folder->getTitle()}</label></h3>
							
							<ol id="folderFiles{$folder->folderID}" style="display: none;">
							{foreach from=$folder->getFiles('image') item=file}
								<li>
									<label><input type="checkbox" name="sectionData[]" value="{$file->fileID}" {if $file->fileID|in_array:$fileIDs}checked="checked" {/if}/>{@$file->getIconTag()} {$file->getTitle()}</label>
								</li>
							{/foreach}
							</ol>
						</div>
					</div>
				</li>
				{/foreach}
			</ol>

			<dl>
				<dt><label for="subtitle">{lang}cms.acp.content.section.data.subtitle{/lang}</label></dt>
				<dd><input type="text" name="subtitle" class="long" id="subtitle" value="{$subtitle}" /></dd>
			</dl>
			<dl>
				<dt><label for="link">{lang}cms.acp.content.section.data.link{/lang}</label></dt>
				<dd><input type="text" name="link" class="long" id="link" value="{$link}" /></dd>
			</dl>
			<dl>

			<dl>
				<dt><label for="resizable">{lang}cms.acp.content.section.data.resizable{/lang}</label></dt>
				<dd><input type="checkbox" name="resizable" id="resizable" value="1" {if $resizable == 1}checked="checked"{/if} /></dd>
			</dl>