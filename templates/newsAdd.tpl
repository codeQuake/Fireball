{include file='documentHeader'}
<head>
    <title>{lang}cms.news.{$action}{/lang} - {lang}cms.page.news{/lang} - {PAGE_TITLE|language}</title>
    {include file='headInclude' application='wcf'}
    <script data-relocate="true">
        //<![CDATA[
        $(function () {
            new WCF.Category.NestedList();
            new WCF.Message.FormGuard();
        });
        //]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">
{include file='header'}
    <header class="boxHeadline">
	<h1>{lang}cms.news.{@$action}{/lang}</h1>
</header>

{include file='userNotice'}

    <form id="messageContainer" class="jsFormGuard" method="post" action="{if $action == 'add'}{link controller='NewsAdd' application='cms'}{/link}{else}{link controller='NewsEdit' application='cms' id=$newsID}{/link}{/if}">
        <div class="container containerPadding marginTop">
            <fieldset>
                <legend>{lang}cms.news.category.categories{/lang}</legend>
                <small>{lang}cms.news.category.categories.description{/lang}</small>
                <ol class="nestedCategoryList doubleColumned jsCategoryList">
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
                {if $errorField == 'categoryIDs'}
				<small class="innerError">
					{if $errorType == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}cms.news.categories.error.{@$errorType}{/lang}
					{/if}
				</small>
			    {/if}
                {event name='categoryFields'}
            </fieldset>

            <fieldset>
                <legend>{lang}cms.news.general{/lang}</legend>
                {if $action =='add'}{include file='messageFormMultilingualism'}{/if}

                <dl{if $errorField == 'subject'} class="formError"{/if}>
				    <dt><label for="subject">{lang}wcf.global.title{/lang}</label></dt>
				    <dd>
					    <input type="text" id="subject" name="subject" value="{$subject}" required="required" maxlength="255" class="long" />
					    {if $errorField == 'subject'}
						    <small class="innerError">
							    {if $errorType == 'empty'}
								    {lang}wcf.global.form.error.empty{/lang}
							    {elseif $errorType == 'censoredWordsFound'}
								    {lang}wcf.message.error.censoredWordsFound{/lang}
							    {else}
								    {lang}cms.news.subject.error.{@$errorType}{/lang}
							    {/if}
						    </small>
					    {/if}
				    </dd>
			    </dl>
                
			{event name='informationFields'}
            </fieldset>
            <fieldset>
			    <legend>{lang}cms.news.message{/lang}</legend>
			
			    <dl class="wide{if $errorField == 'text'} formError{/if}">
				    <dt><label for="text">{lang}cms.news.message{/lang}</label></dt>
				    <dd>
					    <textarea id="text" name="text" rows="20" cols="40">{$text}</textarea>
					    {if $errorField == 'text'}
						    <small class="innerError">
							    {if $errorType == 'empty'}
								    {lang}wcf.global.form.error.empty{/lang}
							    {elseif $errorType == 'tooLong'}
								    {lang}wcf.message.error.tooLong{/lang}
							    {elseif $errorType == 'censoredWordsFound'}
								    {lang}wcf.message.error.censoredWordsFound{/lang}
							    {else}
								    {lang}cms.news.message.error.{@$errorType}{/lang}
							    {/if}
						    </small>
					    {/if}
				    </dd>
			    </dl>
			
			{event name='messageFields'}
		</fieldset>
        {event name='fieldsets'}
		
		{include file='messageFormTabs' wysiwygContainerID='text'}
        </div>

        <div class="formSubmit">
		    <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		    {include file='messageFormPreviewButton'}
	    </div>
    </form>
{include file='footer'}
{include file='wysiwyg'}

</body>
</html>