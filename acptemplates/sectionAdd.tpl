{capture assign='pageTitle'}{lang}cms.acp.content.section.{@$action}{/lang}{/capture}
{include file='header'}

{include file='multipleLanguageInputJavascript' elementIdentifier='sectionData' forceSelection=false}

<header class="boxHeadline">
    <h1>{lang}cms.acp.content.section.{@$action}{/lang}</h1>
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
            <li><a href="{link application='cms' controller='ContentSectionList' id=$contentID}{/link}" title="{lang}cms.acp.menu.link.cms.content.section.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}cms.acp.menu.link.cms.content.section.list{/lang}</span></a></li>
            {event name='contentNavigationButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='cms' controller='ContentSectionAdd' id=$contentID}{if $objectType->objectType|isset}objectType={$objectType}{/if}{/link}{else}{link application='cms' controller='ContentSectionEdit' id=$sectionID}{/link}{/if}">
    <div class="container containerPadding marginTop shadow">
        <fieldset>
            <legend>{lang}cms.acp.content.section.general{/lang}</legend>
            <dl>
                <dt><label for="objectType">{lang}cms.acp.content.section.general.objectType{/lang}</label></dt>
                <dd>
                    <select id="objectType" name="objectType">
                        {foreach from=$objectTypeList item='item'}
						<option value="{$item->objectType}" {if $item->objectType == $objectType}selected="selected"{/if}>{lang}cms.acp.content.section.type.{$item->objectType}{/lang}</option>
						{/foreach}
                    </select>
                </dd>

            </dl>
        </fieldset>
    </div>
</form>