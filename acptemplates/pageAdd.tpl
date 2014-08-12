{include file='header' pageTitle='cms.acp.page.'|concat:$action}
{include file='aclPermissions'}
<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/CMS.ACP.js"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.Language.addObject({
			'cms.acp.page.alias.preview': '{lang}cms.acp.page.alias.preview{/lang}',
		});

		WCF.TabMenu.init();

		new CMS.ACP.Page.AddForm();

	});
	//]]>
</script>

{if $pageID|isset}
	{include file='aclPermissionJavaScript' containerID='userPermissionsContainer' objectID=$pageID}
{else}
	{include file='aclPermissionJavaScript' containerID='userPermissionsContainer'}
{/if}


<header class="boxHeadline">
	<h1>{lang}cms.acp.page.{@$action}{/lang}</h1>
	{if $action == 'edit'}<p>{$page->getTitle()}</p>{/if}
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link application='cms' controller='PageList'}{/link}" title="{lang}cms.acp.menu.link.cms.page.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}cms.acp.menu.link.cms.page.list{/lang}</span></a></li>

			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='cms' controller='PageAdd'}{/link}{else}{link application='cms' controller='PageEdit' id=$pageID}{/link}{/if}">
	<div class="tabMenuContainer" data-active="{$activeTabMenuItem}" data-store="activeTabMenuItem">
		<nav class="tabMenu">
			<ul>
				<li><a href="{@$__wcf->getAnchor('general')}">{lang}cms.acp.page.general{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('pageStylesheets')}">{lang}cms.acp.page.stylesheets{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('userPermissions')}">{lang}cms.acp.page.userPermissions{/lang}</a></li>
				{event name='tabMenuTabs'}
			</ul>
		</nav>

		<div id="general" class="container containerPadding tabMenuContent">
			<fieldset>
				<legend>{lang}wcf.global.form.data{/lang}</legend>

				<dl{if $errorField == 'title'} class="formError"{/if}>
					<dt><label for="title">{lang}cms.acp.page.title{/lang}</label></dt>
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

						{include file='multipleLanguageInputJavascript' elementIdentifier='title' forceSelection=true}
					</dd>
				</dl>

				<dl{if $errorField == 'alias'} class="formError"{/if}>
					<dt><label for="alias">{lang}cms.acp.page.alias{/lang}</label></dt>
					<dd>
						<input type="text" id="alias" name="alias" value="{$alias}" class="long" required="required" />
						{if $errorField == 'alias'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}cms.acp.page.alias.error.{$errorType}{/lang}
								{/if}
							</small>
						{/if}
						<small>{lang}cms.acp.page.alias.description{/lang}</small>
						<small id="aliasPreview"></small>
					</dd>
				</dl>

				<dl{if $errorField == 'description'} class="formError"{/if}>
					<dt><label for="description">{lang}cms.acp.page.general.description{/lang}</label></dt>
					<dd>
						<textarea id="description" name="description" rows="5" cols="40" class="long">{$i18nPlainValues['description']}</textarea>
						{if $errorField == 'description'}
							<small class="innerError">
								{lang}cms.acp.page.description.error.{@$errorType}{/lang}
							</small>
						{/if}

						{include file='multipleLanguageInputJavascript' elementIdentifier='description' forceSelection=false}
					</dd>
				</dl>

				<dl{if $errorField == 'menuItem'} class="formError"{/if}>
					<dt class="reversed"><label for="menuItem">{lang}cms.acp.page.general.menuItem{/lang}</label></dt>
					<dd>
						<input type="checkbox" name="menuItem" id="menuItem" value="1"{if $menu == 1} checked="checked"{/if} />
						{if $errorField == 'menuItem'}
							<small class="innerError">
								{lang}cms.acp.page.general.menuItem.error.{@$errorType}{/lang}
							</small>
						{/if}
						<small>{lang}cms.acp.page.general.menuItem.description{/lang}</small>
					</dd>
				</dl>

				{event name='dataFields'}
			</fieldset>

			<fieldset>
				<legend>{lang}cms.acp.page.meta{/lang}</legend>

				<dl{if $errorField == 'metaDescription'} class="formError"{/if}>
					<dt><label for="metaDescription">{lang}cms.acp.page.meta.metaDescription{/lang}</label></dt>
					<dd>
						<textarea id="metaDescription" name="metaDescription" rows="5" cols="40" class="long">{$i18nPlainValues['metaDescription']}</textarea>
						<small>{lang}cms.acp.page.metaDescription.description{/lang}</small>
						{if $errorField == 'metaDescription'}
							<small class="innerError">
								{lang}cms.acp.page.metaDescription.error.{@$errorType}{/lang}
							</small>
						{/if}

						{include file='multipleLanguageInputJavascript' elementIdentifier='metaDescription' forceSelection=false}
					</dd>
				</dl>

				<dl{if $errorField == 'metaKeywords'} class="formError"{/if}>
					<dt><label for="metaKeywords">{lang}cms.acp.page.meta.metaKeywords{/lang}</label></dt>
					<dd>
						<input type="text" id="metaKeywords" name="metaKeywords" value="{$i18nPlainValues['metaKeywords']}" class="long" />
						{if $errorField == 'metaKeywords'}
							<small class="innerError">
								{lang}cms.acp.page.metaKeywords.error.{@$errorType}{/lang}
							</small>
						{/if}

						{include file='multipleLanguageInputJavascript' elementIdentifier='metaKeywords' forceSelection=false}
					</dd>
				</dl>

				<dl{if $errorField == 'robots'} class="formError"{/if}>
					<dt><label for="robots">{lang}cms.acp.page.meta.robots{/lang}</label></dt>
					<dd>
						<select id="robots" name="robots">
							<option value="index,follow"{if $robots =="index,follow"} selected="selected"{/if}>{lang}cms.acp.page.meta.robots.indexfollow{/lang}</option>
							<option value="index,nofollow"{if $robots =="index,nofollow"} selected="selected"{/if}>{lang}cms.acp.page.meta.robots.indexnofollow{/lang}</option>
							<option value="noindex,follow"{if $robots =="noindex,follow"} selected="selected"{/if}>{lang}cms.acp.page.meta.robots.noindexfollow{/lang}</option>
							<option value="noindex,nofollow"{if $robots =="noindex,nofollow"} selected="selected"{/if}>{lang}cms.acp.page.meta.robots.noindexnofollow{/lang}</option>
						</select>
					</dd>
				</dl>

				{event name='metaFields'}
			</fieldset>

			<fieldset>
				<legend>{lang}cms.acp.page.position{/lang}</legend>

				{hascontent}
					<dl{if $errorField == 'parentID'} class="formError"{/if}>
						<dt><label for="parentID">{lang}cms.acp.page.general.parentID{/lang}</label></dt>
						<dd>
							<select id="parentID" name="parentID">
								<option value="0" {if $parentID == 0} selected="selected"{/if} data-alias="">{lang}wcf.global.noSelection{/lang}</option>
								{content}
									{foreach from=$pageList item=$node}
										<option data-alias="{$node->getAlias()}" {if $node->pageID == $parentID} selected="selected" {/if} value="{@$node->pageID}">{section name=i loop=$pageList->getDepth()}&nbsp;&raquo;&raquo;&nbsp;{/section}{$node->getTitle()|language}</option>
									{/foreach}
								{/content}
							</select>
						</dd>
					</dl>
				{/hascontent}

				<dl{if $errorField == 'showOrder'} class="formError"{/if}>
					<dt><label for="showOrder">{lang}cms.acp.page.position{/lang}</label></dt>
					<dd>
						<input type="number" id="showOrder" name="showOrder" value="{$showOrder}" class="tiny" min="0" />
						{if $errorField == 'showOrder'}
							<small class="innerError">
								{lang}cms.acp.page.position.error.{@$errorType}{/lang}
							</small>
						{/if}
						<small>{lang}cms.acp.page.position.description{/lang}</small>
					</dd>
				</dl>

				<dl{if $errorField == 'invisible'} class="formError"{/if}>
					<dt class="reversed"><label for="invisible">{lang}cms.acp.page.position.invisible{/lang}</label></dt>
					<dd>
						<input type="checkbox" name="invisible" id="invisible" value="1"{if $invisible} checked="checked"{/if} />
						<small>{lang}cms.acp.page.position.invisible.description{/lang}</small>
					</dd>
				</dl>

				{event name='positionFields'}
			</fieldset>

			<fieldset>
				<legend>{lang}cms.acp.page.settings{/lang}</legend>

				<dl{if $errorField == 'showSidebar'} class="formError"{/if}>
					<dt class="reversed"><label for="showSidebar">{lang}cms.acp.page.settings.showSidebar{/lang}</label></dt>
					<dd>
						<input type="checkbox" name="showSidebar" id="showSidebar" value="1"{if $showSidebar} checked="checked"{/if} />
						<small>{lang}cms.acp.page.settings.showSidebar.description{/lang}</small>
					</dd>
				</dl>

				<dl{if $errorField == 'sidebarOrientation'} class="formError"{/if}>
					<dt><label for="sidebarOrientation">{lang}cms.acp.page.settings.sidebarOrientation{/lang}</label></dt>
					<dd>
						<select id="sidebarOrientation" name="sidebarOrientation">
							<option value="right"{if $sidebarOrientation =="right"} selected="selected"{/if}>{lang}cms.acp.page.settings.sidebarOrientation.right{/lang}</option>
							<option value="left"{if $sidebarOrientation =="left"} selected="selected"{/if}>{lang}cms.acp.page.settings.sidebarOrientation.left{/lang}</option>
						</select>
					</dd>
				</dl>

				<dl{if $errorField == 'isCommentable'} class="formError"{/if}>
					<dt class="reversed"><label for="isCommentable">{lang}cms.acp.page.settings.isCommentable{/lang}</label></dt>
					<dd>
						<input type="checkbox" name="isCommentable" id="isCommentable" value="1"{if $isCommentable == 1} checked="checked"{/if} />
						<small>{lang}cms.acp.page.settings.isCommentable.description{/lang}</small>
					</dd>
				</dl>

				<dl{if $errorField == 'availableDuringOfflineMode'} class="formError"{/if}>
					<dt class="reversed"><label for="availableDuringOfflineMode">{lang}cms.acp.page.settings.availableDuringOfflineMode{/lang}</label></dt>
					<dd>
						<input type="checkbox" name="availableDuringOfflineMode" id="availableDuringOfflineMode" value="1"{if $availableDuringOfflineMode} checked="checked"{/if} />
					</dd>
				</dl>

				<dl>
					<dt><label for="styleID">{lang}cms.acp.page.styleID{/lang}</label></dt>
					<dd>
						<select id="styleID" name="styleID">
							<option value="0">{lang}wcf.global.noSelection{/lang}</option>
							{foreach from=$availableStyles item=style}
								<option value="{@$style->styleID}"{if $style->styleID == $styleID} selected="selected"{/if}>{$style->styleName}</option>
							{/foreach}
						</select>
						<small>{lang}cms.acp.page.styleID.description{/lang}</small>
					</dd>
				</dl>

			</fieldset>

			{event name='fieldsets'}
		</div>

		<div id="pageStylesheets" class="container containerPadding tabMenuContent">
			<fieldset>
				<legend>{lang}cms.acp.page.stylesheets{/lang}</legend>

				<dl>
					<dt>{lang}cms.acp.page.stylesheets.select{/lang}</dt>

					<dd>
						<select name="stylesheets[]" multiple="multiple" id="stylesheets" size="10">
							{foreach from=$stylesheetList item=$sheet}
								<option value="{$sheet->sheetID}" {if $sheet->sheetID|in_array:$stylesheets}selected="selected"{/if}>{$sheet->title}</option>
							{/foreach}
						</select>
						<small>{lang}wcf.global.multiSelect{/lang}</small>
						{if $errorField == 'stylesheets'}
								<small class="innerError">
									{lang}cms.acp.page.stylesheets.error.{@$errorType}{/lang}
								</small>
						{/if}
					</dd>
				</dl>
			</fieldset>
		</div>

		<div id="userPermissions" class="container containerPadding tabMenuContent">
			<fieldset>
				<legend>{lang}wcf.acl.permissions{/lang}</legend>

				<dl id="userPermissionsContainer" class="wide">
					<dd></dd>
				</dl>
			</fieldset>
		</div>

		{event name='tabMenuContents'}
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
		<input type="hidden" name="action" value="{@$action}" />
		{if $pageID|isset}<input type="hidden" name="id" value="{@$pageID}" />{/if}
	</div>
</form>

{include file='footer'}
