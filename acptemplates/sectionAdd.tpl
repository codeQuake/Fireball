{capture assign='pageTitle'}{lang}cms.acp.content.section.{@$action}{/lang}{/capture}
{include file='header'}

	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Like.js"></script>
	<script data-relocate="true" src="{@$__wcf->getPath('cms')}js/CMS.js"></script>
	<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js"></script>


<nav class="breadcrumbs marginTop">
	<ul>
		<li title="{lang}cms.acp.page.overview{/lang}" itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
			<a href="{link controller='Overview' application='cms'}{/link}" itemprop="url">
				<span itemprop="title">{lang}cms.acp.page.overview{/lang}</span>
			</a>
			<span class="pointer">
				<span>»</span>
			</span>
		</li>
		<li title="{$content->getPage()->getTitle()|language}" itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
			<a href="{link controller='PageEdit' application='cms' id=$content->getPage()->pageID}{/link}" itemprop="url">
				<span itemprop="title">{$content->getPage()->getTitle()|language}</span>
			</a>
			<span class="pointer">
				<span>»</span>
			</span>
		</li>
		<li title="{$content->getTitle()|language}" itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
			<a href="{link controller='PageEdit' application='cms' id=$content->getPage()->pageID}#contents{/link}" itemprop="url">
				<span itemprop="title">{$content->getTitle()|language}</span>
			</a>
			<span class="pointer">
				<span>»</span>
			</span>
		</li>
	</ul>
</nav>

<header class="boxHeadline">
    <h1>{lang}cms.acp.content.section.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
    <nav>
        <ul>
			<li><a href="{link controller='Overview' application='cms'}{/link}" class="button"><span class="icon icon16 icon-gears"></span> <span>{lang}cms.acp.page.overview{/lang}</span></a></li>
            <li><a href="{link application='cms' controller='ContentSectionList' id=$contentID}{/link}" title="{lang}cms.acp.menu.link.cms.content.section.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}cms.acp.menu.link.cms.content.section.list{/lang}</span></a></li>
            {event name='contentNavigationButtons'}
        </ul>
    </nav>
</div>

<form {if $objectTypeName != 'de.codequake.cms.section.type.text'} id="formContainer" {else} id="messageContainer"{/if} method="post" action="{if $action == 'add'}{link application='cms' controller='ContentSectionAdd' id=$contentID}{/link}{else}{link application='cms' controller='ContentSectionEdit' id=$sectionID}{/link}{/if}">
    <div class="container containerPadding marginTop shadow">
        <fieldset>
            <legend>{lang}cms.acp.content.section.general{/lang}</legend>
            <dl>
                <dt><label for="objectType">{lang}cms.acp.content.section.general.objectType{/lang}</label></dt>
                <dd>
                    <select id="objectType" name="objectType" onchange="this.form.submit()">
						<option value="none"></option>
                        {foreach from=$objectTypeList item='item'}
						<option value="{$item->objectType}" {if $item->objectType == $objectTypeName}selected="selected"{/if}>{lang}cms.acp.content.section.type.{$item->objectType}{/lang}</option>
						{/foreach}
                    </select>
                </dd>
            </dl>
        </fieldset>


        {if $objectType != null}
        <fieldset>
            <legend>{lang}cms.acp.content.section.data{/lang}</legend>
            {include file=$objectType->getProcessor()->getFormTemplate() application='cms'}
        </fieldset>
        {/if}


		<fieldset>
			<legend>{lang}cms.acp.content.section.css{/lang}</legend>
			<dl>
				<dt><label for="cssID">{lang}cms.acp.content.section.css.cssID{/lang}</label></dt>
				<dd>
					<input type="text" name="cssID" id="cssID" class="long" value="{$cssID}" />
				</dd>
			</dl>
			<dl>
				<dt><label for="cssClasses">{lang}cms.acp.content.section.css.cssClasses{/lang}</label></dt>
				<dd>
					<input type="text" name="cssClasses" id="cssClasses" class="long" value="{$cssClasses}" />
					{if $objectTypeName == 'de.codequake.cms.section.type.headline'}
						<small>{lang}cms.acp.content.section.css.cssClasses.headlineDescription{/lang}</small>
					{/if}
				</dd>
			</dl>
		</fieldset>

		<fieldset>
			<legend>{lang}cms.acp.content.section.showOrder{/lang}</legend>
			<dl>
				<dt><label for="showOrder">{lang}cms.acp.content.section.showOrder.showOrder{/lang}</label></dt>
				<dd>
					<input type="text" name="showOrder" id="showOrder" value="{$showOrder}" />
				</dd>
			</dl>
		</fieldset>
    </div>
    <div class="formSubmit">
        <input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
        <input type="submit" name="send" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
         {@SECURITY_TOKEN_INPUT_TAG}
        <input type="hidden" name="action" value="{@$action}" />
        {if $contentID|isset}<input type="hidden" name="id" value="{@$contentID}" />{/if}
		<!--leave the unused sections here for later ;) -->
		{if $objectTypeName != 'de.codequake.cms.section.type.text' && $objectTypeName != 'de.codequake.cms.section.type.image' && $objectTypeName != 'de.codequake.cms.section.type.news' && $objectTypeName != 'de.codequake.cms.section.type.module' && $objectTypeName != 'de.codequake.cms.section.type.dashboard'}<input id="previewButton" type="button" class="jsOnly" accesskey="p" value="{lang}wcf.global.button.preview{/lang}" />
			<script data-relocate="true">
			//<![CDATA[
			$(function() {
				WCF.Language.addObject({
					'wcf.global.preview': '{lang}wcf.global.preview{/lang}'
				});
				new CMS.ACP.Content.Preview('{$objectTypeName}');
			});
			//]]>
			</script>
		{elseif $objectTypeName == 'de.codequake.cms.section.type.text'}
		{include file='messageFormPreviewButton'}
		{/if}

    </div>
</form>

{include file='footer'}
