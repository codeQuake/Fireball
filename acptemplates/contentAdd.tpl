{include file='header' pageTitle='cms.acp.content.'|concat:$action}

<nav class="breadcrumbs marginTop">
	<ul>
		{if $pageID != 0}
			<li title="{$page->getTitle()|language}" itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">
				<a href="{link controller='PageEdit' application='cms' id=$page->pageID}{/link}" itemprop="url">
					<span itemprop="title">{$page->getTitle()|language}</span>
				</a>
				<span class="pointer">
					<span>»</span>
				</span>
			</li>
		{/if}
	</ul>
</nav>

<header class="boxHeadline">
	<h1>{lang}cms.acp.content.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link application='cms' controller='ContentList' pageID=$page->pageID}{/link}" title="{lang}cms.acp.content.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}cms.acp.content.list{/lang}</span></a></li>

			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='cms' controller='ContentAdd' id=$pageID}objectType={$objectType->objectType}{if $position|isset}&position={$position}{/if}{/link}{else}{link application='cms' controller='ContentEdit' id=$contentID}objectType={$objectType->objectType}{if $position|isset}&position={$position}{/if}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.global.form.data{/lang}</legend>

			<dl{if $errorField == 'title'} class="formError"{/if}>
				<dt><label for="title">{lang}wcf.global.title{/lang}</label></dt>
				<dd>
					<input type="text" id="title" name="title" value="{$i18nPlainValues['title']}" class="long" required="required" />
					{if $errorField == 'title'}
						<small class="innerError">
							{if $errorType == 'empty' || $errorType == 'multilingual'}
								{lang}wcf.global.form.error.{@$errorType}{/lang}
							{else}
								{lang}cms.acp.content.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}

					{include file='multipleLanguageInputJavascript' elementIdentifier='title' forceSelection=false}
				</dd>
			</dl>

			{event name='dataFields'}
		</fieldset>

		<fieldset>
			<legend>{lang}cms.acp.content.type.{$objectType->objectType}{/lang}</legend>

			{include file=$objectTypeProcessor->getFormTemplate() application='cms'}
		</fieldset>

		<fieldset>
			<legend>{lang}cms.acp.content.css{/lang}</legend>

			<dl{if $errorField == 'cssID'} class="formError"{/if}>
				<dt><label for="cssID">{lang}cms.acp.content.css.cssID{/lang}</label></dt>
				<dd>
					<input type="text" id="cssID" name="cssID" value="{$cssID}" class="long" />
					{if $errorField == 'cssID'}
						<small class="innerError">
							{lang}cms.acp.content.cssID.error.{@$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>

			<dl{if $errorField == 'cssClasses'} class="formError"{/if}>
				<dt><label for="cssClasses">{lang}cms.acp.content.css.cssClasses{/lang}</label></dt>
				<dd>
					<input type="text" id="cssClasses" name="cssClasses" value="{$cssClasses}" class="long" />
					{if $errorField == 'cssClasses'}
						<small class="innerError">
							{lang}cms.acp.content.cssClasses.error.{@$errorType}{/lang}
						</small>
					{/if}
					<small>{lang}cms.acp.content.css.cssClasses.description{/lang}</small>
				</dd>
			</dl>

			{event name='cssFields'}
		</fieldset>

		<fieldset>
			<legend>{lang}cms.acp.content.position{/lang}</legend>

			<dl>
				<dt><label for="parentID">{lang}cms.acp.content.position.parentID{/lang}</label></dt>
				<dd>
					<select id="parentID" name="parentID">
						<option value="0" {if $parentID == 0} selected="selected"{/if}>{lang}wcf.global.noSelection{/lang}</option>
						{foreach from=$contentList item=$node}
							<option {if $node->contentID == $parentID} selected="selected" {/if} value="{@$node->contentID}">{section name=i loop=$contentList->getDepth()}&nbsp;&raquo;&raquo;&nbsp;{/section}{$node->getTitle()|language}</option>
						{/foreach}
					</select>
				</dd>
			</dl>

			<dl>
				<dt><label for="showOrder">{lang}cms.acp.content.position.showOrder{/lang}</label></dt>
				<dd>
					<input type="number" name="showOrder" id="showorder" value="{$showOrder}" />
				</dd>
			</dl>

			{event name='positionFields'}
		</fieldset>

		{event name='fieldsets'}
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		<input type="reset" value="{lang}wcf.global.button.reset{/lang}" accesskey="r" />
		{@SECURITY_TOKEN_INPUT_TAG}
		<input type="hidden" name="action" value="{@$action}" />
		<input type="hidden" name="objectType" value="{@$objectType->objectType}" />
		{if $contentID|isset}<input type="hidden" name="contentID" value="{@$contentID}" />{/if}
		{if $pageID|isset}<input type="hidden" name="pageID" value="{@$pageID}" />{/if}
		{if $position|isset}<input type="hidden" name="position" value="{@$position}" />{/if}
	</div>
</form>

{include file='footer'}
