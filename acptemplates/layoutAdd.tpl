{capture assign='pageTitle'}{lang}cms.acp.layout.{@$action}{/lang}{/capture}
{include file='header'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.layout.{@$action}{/lang}</h1>
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
            <li><a href="{link application='cms' controller='LayoutList'}{/link}" title="{lang}cms.acp.menu.link.cms.layout.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}cms.acp.menu.link.cms.layout.list{/lang}</span></a></li>
            {event name='contentNavigationButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='cms' controller='LayoutAdd'}{/link}{else}{link application='cms' controller='LayoutEdit' id=$layoutID}{/link}{/if}">
    <div class="marginTop container shadow containerPadding">
        <fieldset>
            <legend>{lang}cms.acp.layout.general{/lang}</legend>
            <dl>
                <dt><label for="title">{lang}cms.acp.layout.title{/lang}</label></dt>
                <dd>
                    <input type="text"  id="title" name="title" value="{$title}"/>
                </dd>
            </dl>
        </fieldset>
        <fieldset>
            <legend>{lang}cms.acp.layout.options{/lang}</legend>
                <dl>
                    <dt><label for="stylesheets">{lang}cms.acp.layout.stylesheets{/lang}</label></dt>
                    <dd>
                        <select name="data[]" multiple="multiple" id="stylesheets" size="10">
                            {foreach from=$sheetList item=$sheet}
                                <option value="{$sheet->sheetID}" {if $sheet->sheetID|in_array:$data}selected="selected"{/if}>{$sheet->title}</option>
                            {/foreach}
                        </select>
                        <small>{lang}wcf.global.multiSelect{/lang}</small>
                    </dd>
                </dl>
        </fieldset>
    </div>
    <div class="formSubmit">
				<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
				{@SID_INPUT_TAG}
				<input type="hidden" name="action" value="{@$action}" />
			</div>
</form>

{include file='footer'}