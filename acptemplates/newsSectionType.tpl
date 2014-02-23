<dl>
<dt><label for="categoryList">{lang}cms.news.category.categories{/lang}</label></dt>
<dd><ol id="categoryList" class="nestedCategoryList doubleColumned jsCategoryList">
				{foreach from=$categoryList item=categoryItem}
					<li>
						<div>
							<div class="containerHeadline">
								<h3><label{if $categoryItem->getDescription()} class="jsTooltip" title="{$categoryItem->getDescription()}"{/if}><input type="checkbox" name="categoryIDs[]" value="{@$categoryItem->categoryID}" class="jsCategory"{if $categoryItem->categoryID|in_array:$categoryIDs}checked="checked" {/if}/> {$categoryItem->getTitle()}</label></h3>
							</div>
							
							{if $categoryItem->hasChildren()}
								<ol>
									{foreach from=$categoryItem item=subCategoryItem}
										<li>
											<label{if $subCategoryItem->getDescription()} class="jsTooltip" title="{$subCategoryItem->getDescription()}"{/if}><input type="checkbox" name="categoryIDs[]" value="{@$subCategoryItem->categoryID}" class="jsChildCategory"{if $subCategoryItem->categoryID|in_array:$categoryIDs}checked="checked" {/if}/> {$subCategoryItem->getTitle()}</label>
										</li>
									{/foreach}
								</ol>
							{/if}
						</div>
					</li>
				{/foreach}
			</ol>
    </dd>
</dl>
<dl>
  <dt>
    <label for="limit">{lang}cms.acp.content.section.news.limit{/lang}</label>
  </dt>
  <dd>
    <input type="number" name="limit" id="limit" value="{$limit}" />
  </dd>
</dl>
<dl>
		<dt><label for="type">{lang}cms.acp.content.section.news.type{/lang}</label></dt>
		<dd>
      <select id="type" name="type">
      <option value="0" {if $type == 0}selected="selected"{/if}>{lang}cms.acp.content.section.news.type.0{/lang}</option>
      <option value="1" {if $type == 1}selected="selected"{/if}>{lang}cms.acp.content.section.news.type.1{/lang}</option>
      </select>
		</dd>
</dl>