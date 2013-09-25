{capture assign='pageTitle'}{lang}cms.acp.page.{@$action}{/lang}{/capture}
{include file='header'}

{include file='multipleLanguageInputJavascript' elementIdentifier='title' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='description' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='metaDescription' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='metaKeywords' forceSelection=false}

<header class="boxHeadline">
    <h1>{lang}cms.acp.page.{@$action}{/lang}</h1>
</header>

{if $errorField}
<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
    <nav>
        <ul>
            <li><a href="{link application='cms' controller='PageList'}{/link}" title="{lang}cms.acp.menu.link.cms.page.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}cms.acp.menu.link.cms.page.list{/lang}</span></a></li>
            {event name='contentNavigationButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='cms' controller='PageAdd'}{/link}{else}{link application='cms' controller='PageEdit'}{/link}{/if}">
    <div class="container containerPadding marginTop shadow">
        <fieldset>
            <legend>{lang}cms.acp.page.general{/lang}</legend>
            <dl {if $errorField == 'title'}class="formError"{/if}>
                <dt><label for="title">{lang}cms.acp.page.general.title{/lang}</label></dt>
                <dd>
                    <input type="text" id="title" name="title" value="{$i18nPlainValues['title']}" class="long" required="required" />
                    {if $errorField == 'title'}
                        <small class="innerError">
                            {if $errorType == 'empty'}
                            {lang}wcf.global.form.error.empty{/lang}
                            {else}
                            {lang}cms.acp.page.title.error.{@$errorType}{/lang}
                            {/if}
                        </small>
                    {/if}
                </dd>
            </dl>
            <dl>
                <dt><label for="description">{lang}cms.acp.page.general.description{/lang}</label></dt>
                <dd>
                    <textarea id="description" name="description" rows="5" cols="40" class="long">{$i18nPlainValues['description']}</textarea>
                    {if $errorField == 'description'}
                        <small class="innerError">
                            {lang}cms.acp.page.description.error.{@$errorType}{/lang}
                        </small>
                    {/if}
                </dd>
            </dl>
        </fieldset>
        <fieldset>
            <legend>{lang}cms.acp.page.meta{/lang}</legend>
            <dl>
                <dt><label for="metaDescription">{lang}cms.acp.page.meta.metaDescription{/lang}</label></dt>
                <dd>
                    <textarea id="metaDescription" name="metaDescription" rows="5" cols="40" class="long">{$i18nPlainValues['metaDescription']}</textarea>
                    {if $errorField == 'metaDescription'}
                        <small class="innerError">
                            {lang}cms.acp.page.metaDescription.error.{@$errorType}{/lang}
                        </small>
                    {/if}
                </dd>
            </dl>
            <dl>
                <dt><label for="metaKeywords">{lang}cms.acp.page.meta.metaKeywords{/lang}</label></dt>
                <dd>
                     <input type="text" id="metaKeywords" name="metaKeywords" value="{$i18nPlainValues['metaKeywords']}" class="long" />
                    {if $errorField == 'metaKeywords'}
                        <small class="innerError">
                            {lang}cms.acp.page.metaKeywords.error.{@$errorType}{/lang}
                        </small>
                    {/if}
                </dd>
            </dl>

            <dl>
                <dt><label for="robots">{lang}cms.acp.page.meta.robots{/lang}</label></dt>
                <dd>
                    <select id="robots" name="robots">
                        <option value="index,follow" {if $robots =="index,follow"}selected="selected"{/if}>{lang}cms.acp.page.meta.robots.indexfollow{/lang}</option>
                        <option value="index,nofollow {if $robots =="index,nofollow"}selected="selected"{/if}">{lang}cms.acp.page.meta.robots.indexnofollow{/lang}</option>
                        <option value="noindex,follow {if $robots =="noindex,follow"}selected="selected"{/if}">{lang}cms.acp.page.meta.robots.noindexfollow{/lang}</option>
                        <option value="noindex,nofollow {if $robots =="noindex,nofollow"}selected="selected"{/if}">{lang}cms.acp.page.meta.robots.noindexnofollow{/lang}</option>
                    </select>
                </dd>
            </dl>
        </fieldset>
        <fieldset>
            <legend>{lang}cms.acp.page.visibility{/lang}</legend>
            <dl>
                <dt><label for="invisible">{lang}cms.acp.page.visibility.invisible{/lang}</label></dt>
                <dd>
                    <input type="checkbox" name="invisible" id="invisible" value="1" {if $invisible == 1}checked="checked"{/if} />
                </dd>
            </dl>
        </fieldset>
        {event name='fieldsets'}
    </div>
    <div class="formSubmit">
        <input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
        {@SID_INPUT_TAG}
        <input type="hidden" name="action" value="{@$action}" />
        {if $pageID|isset}<input type="hidden" name="id" value="{@$pageID}" />{/if}
    </div>
</form>

{include file='footer'}