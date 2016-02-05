{include file='header' pageTitle='cms.acp.content.'|concat:$action}

<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js?v={@LAST_UPDATE_TIME}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		var $parentContentContainer = [ ];
		var $pageIDInput = $('#pageID');
		var $parentIDInput = $('#parentID');

		function handlePageID() {
			var $pageID = $pageIDInput.val();

			for (var i = 0; i < $parentContentContainer.length; i++) {
				$parentContentContainer[i].appendTo($parentIDInput);
			}

			$parentIDInput.children('option').each(function(index, element) {
				var $option = $(element);

				if ($option.data('pageID') !== undefined && $option.data('pageID') != $pageID) {
					$parentContentContainer.push($option.removeAttr('disabled').detach());
				}
			});
		}

		$pageIDInput.change(handlePageID);
		handlePageID();
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}fireball.acp.content.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			{* <li><a href="{link application='cms' controller='ContentList' pageID=$pageID}{/link}" title="{lang}fireball.acp.content.list{/lang}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}fireball.acp.content.list{/lang}</span></a></li> *}

			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='cms' controller='ContentAdd' id=$pageID}objectType={$objectType->objectType}{/link}{else}{link application='cms' controller='ContentEdit' id=$contentID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.global.form.data{/lang}</legend>

			<dl{if $errorField == 'title'} class="formError"{/if}>
				<dt><label for="title">{lang}wcf.global.title{/lang}</label></dt>
				<dd>
					<input type="text" id="title" name="title" value="{$i18nPlainValues['title']}" class="long" />
					{if $errorField == 'title'}
						<small class="innerError">
							{if $errorType == 'empty' || $errorType == 'multilingual'}
								{lang}wcf.global.form.error.{@$errorType}{/lang}
							{else}
								{lang}fireball.acp.content.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}

					{include file='multipleLanguageInputJavascript' elementIdentifier='title' forceSelection=false}
				</dd>
			</dl>

			{event name='dataFields'}
		</fieldset>

		<fieldset>
			<legend>{lang}fireball.acp.content.position{/lang}</legend>

			<dl>
				<dt><label for="pageID">{lang}fireball.global.page{/lang}</label></dt>
				<dd>
					<select name="pageID" id="pageID">
						{foreach from=$pageList item=$node}
							<option{if $node->pageID == $pageID} selected="selected"{/if} value="{@$node->pageID}">{@"&nbsp;&nbsp;&nbsp;&nbsp;"|str_repeat:$pageList->getDepth()}{$node->getTitle()}</option>
						{/foreach}
					</select>
				</dd>
			</dl>

			{hascontent}
				<dl>
					<dt><label for="parentID">{lang}fireball.acp.content.position.parentID{/lang}</label></dt>
					<dd>
						<select id="parentID" name="parentID">
							<option value="0" {if $parentID == 0} selected="selected"{/if}>{lang}wcf.global.noSelection{/lang}</option>
							{content}
								{foreach from=$contentList item=$node}
									<option{if $node->contentID == $parentID} selected="selected"{/if} value="{@$node->contentID}" data-page-id="{@$node->pageID}">{@"&nbsp;&nbsp;&nbsp;&nbsp;"|str_repeat:$contentList->getDepth()}{$node->getTitle()}</option>
								{/foreach}
							{/content}
						</select>
					</dd>
				</dl>
			{/hascontent}

			<dl>
				<dt><label for="showOrder">{lang}fireball.acp.content.position{/lang}</label></dt>
				<dd>
					<input type="number" name="showOrder" id="showorder" value="{$showOrder}" />
				</dd>
			</dl>

			{event name='positionFields'}
		</fieldset>

		<fieldset>
			<legend>{lang}fireball.acp.content.type.{$objectType->objectType}{/lang}</legend>

			{include file=$objectType->getProcessor()->getFormTemplate() application='cms'}
		</fieldset>

		<fieldset>
			<legend>{lang}fireball.acp.content.css{/lang}</legend>

			<dl{if $errorField == 'cssClasses'} class="formError"{/if}>
				<dt><label for="cssClasses">{lang}fireball.acp.content.css.cssClasses{/lang}</label></dt>
				<dd>
					<input type="text" id="cssClasses" name="cssClasses" value="{$cssClasses}" class="long" />
					{if $errorField == 'cssClasses'}
						<small class="innerError">
							{lang}fireball.acp.content.cssClasses.error.{@$errorType}{/lang}
						</small>
					{/if}
					<small>{lang}fireball.acp.content.css.cssClasses.description{/lang}</small>
				</dd>
			</dl>

			{event name='cssFields'}
		</fieldset>

		{event name='fieldsets'}
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
		<input type="hidden" name="position" value="{$position}" />
	</div>
</form>

{include file='footer'}
