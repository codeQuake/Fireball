
   <script data-relocate="true">
   	 //<![CDATA[
        $(function () {

			new WCF.Category.NestedList();
            });
        //]]>
   </script>
    <ol class="nestedCategoryList doubleColumned jsCategoryList">
	{foreach from=$categoryList item=categoryItem}
		{if $categoryItem->isAccessible()}
		<li>
			<div>
				<div class="containerHeadline">
					<h3><label{if $categoryItem->getDescription()} class="jsTooltip" title="{$categoryItem->getDescription()}"{/if}><input type="checkbox" name="contentData[categoryIDs][]" value="{@$categoryItem->categoryID}" class="jsCategory"{if $contentData[categoryIDs]|isset && $categoryItem->categoryID|in_array:$contentData[categoryIDs]}checked="checked" {/if}/> {$categoryItem->getTitle()}</label></h3>
				</div>

				{if $categoryItem->hasChildren()}
					<ol>
						{foreach from=$categoryItem item=subCategoryItem}
							{if $subCategoryItem->isAccessible()}
							<li>
								<label{if $subCategoryItem->getDescription()} class="jsTooltip" title="{$subCategoryItem->getDescription()}"{/if}><input type="checkbox" name="contentData[categoryIDs][]" value="{@$subCategoryItem->categoryID}" class="jsChildCategory"{if $contentData[categoryIDs]|isset && $subCategoryItem->categoryID|in_array:$contentData[categoryIDs]}checked="checked" {/if}/> {$subCategoryItem->getTitle()}</label>
							</li>
							{/if}
						{/foreach}
					</ol>
				{/if}
			</div>
		</li>
		{/if}
	{/foreach}
</ol>


<dl>
	<dt><label for="contentData[type]">{lang}cms.acp.content.type.de.codequake.cms.content.type.news.type{/lang}</label></dt>
	<dd>
		<select name="contentData[type]" id="contentData[type]">
			<option value="standard" {if $contentData['type']|isset && $contentData['type'] == 'standard'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.news.type.standard{/lang}</option>
			<option value="boxed" {if $contentData['type']|isset && $contentData['type'] == 'boxed'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.news.type.boxed{/lang}</option>
			<option value="simple1" {if $contentData['type']|isset && $contentData['type'] == 'simple1'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.news.type.simple1{/lang}</option>
			<option value="simple2" {if $contentData['type']|isset && $contentData['type'] == 'simple2'}selected="selected"{/if}>{lang}cms.acp.content.type.de.codequake.cms.content.type.news.type.simple2{/lang}</option>
		<select>
	</dd>
</dl>
