{capture assign='pageTitle'}{lang}cms.acp.feed.{@$action}{/lang}{/capture}
{include file='header'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.feed.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}


<div class="contentNavigation">
    <nav>
        <ul>
            <li><a href="{link application='cms' controller='FeedList'}{/link}" title="{lang}cms.acp.menu.link.cms.feed.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}cms.acp.menu.link.cms.feed.list{/lang}</span></a></li>
            {event name='contentNavigationButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='cms' controller='FeedAdd'}{/link}{else}{link application='cms' controller='FeedEdit' id=$feedID}{/link}{/if}">
    <div class="marginTop container shadow containerPadding">
        <fieldset>
            <legend>{lang}cms.acp.feed.general{/lang}</legend>
            <dl>
                <dt><label for="title">{lang}cms.acp.feed.title{/lang}</label></dt>
                <dd>
                    <input type="text"  id="title" name="title" value="{$title}"/>
                </dd>
            </dl>
			<dl>
				<dt><label for="feedUrl">{lang}cms.acp.feed.url{/lang}</label></dt>
				<dd>
				<input type="text"  id="feedUrl" name="feedUrl" value="{$feedUrl}"/>
				</dd>
			</dl>
				<dl{if $errorField == 'languageID'} class="formError"{/if}>
					<dt>{lang}wcf.user.language{/lang}</dt>
					<dd id="languageIDContainer">
							<select name="languageID" id="languageID">
								{foreach from=$availableContentLanguages item=contentLanguage}
									<option value="{@$contentLanguage->languageID}">{$contentLanguage}</option>
								{/foreach}
							</select>
					</dd>
				</dl>
			<dl>
				<dt><label for="categoryID">{lang}cms.acp.feed.categoryID{/lang}</label></dt>
				<dd>
					<select id="categoryID" name="categoryID">
							{foreach from=$categories item=$category}
							<option value="{$category->categoryID}" {if $categoryID == $category->categoryID}selected="selected"{/if}>{$category->getTitle()|language}</option>
							{/foreach}
					</select>
				</dd>
			</dl>
        </fieldset>
        
    </div>
    <div class="formSubmit">
				<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
				 {@SECURITY_TOKEN_INPUT_TAG}
				<input type="hidden" name="action" value="{@$action}" />
			</div>
</form>

{include file='footer'}