{include file='header' pageTitle='cms.acp.page.'|concat:$action}

{include file='aclPermissions'}
<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/Fireball.ACP.js?v={@LAST_UPDATE_TIME}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.Language.addObject({
			'cms.acp.page.alias.preview': '{lang}cms.acp.page.alias.preview{/lang}',
		});

		WCF.TabMenu.init();

		new Fireball.ACP.Page.Alias.Preview('#alias', '#parentID', '{link application="cms" controller="Page" alias="123456789" forceFrontend=true}{/link}');

		{if $action == 'add'}
			$('#createMenuItem').click(function() {
				$('#menuItemID').parents('dl:eq(0)').toggle();
			});
		{/if}

		$('#enableDelayedDeactivation, #enableDelayedPublication').click(function() {
			var $toggleContainerID = $(this).data('toggleContainer');
			$('#'+ $toggleContainerID).toggle();
		});

		new Fireball.ACP.Page.TypePicker({if $pageObjectTypeID}{$pageObjectTypeID}{/if}{if !$pageID|empty}, {$pageID}{/if});
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
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>		
			{if $action == 'edit'}
				<li>
					<a class="button" href="{link application='cms' controller='ContentList' pageID=$pageID}{/link}"><span class="icon icon16 icon-file"></span> <span>{lang}cms.acp.content.list{/lang}</span></a>
				</li>
			{/if}
			{if $action == 'edit' && $choosePageNodeList|iterator_count > 1}
				<li class="dropdown">
					<a class="button dropdownToggle"><span class="icon icon16 icon-sort"></span> <span>{lang}cms.acp.page.button.choose{/lang}</span></a>
					<div class="dropdownMenu">
						<ul class="scrollableDropdownMenu">
							{foreach from=$choosePageNodeList item=node}
								<li{if $node->pageID == $pageID} class="active"{/if}><a href="{link application='cms' controller='PageEdit' id=$node->pageID}{/link}">{@"&nbsp;&nbsp;&nbsp;&nbsp;"|str_repeat:$choosePageNodeList->getDepth()}{$node->getTitle()}</a></li>
							{/foreach}
						</ul>
					</div>
				</li>
			{/if}
			<li><a href="{link application='cms' controller='PageList'}{/link}" title="{lang}cms.acp.menu.link.cms.page.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}cms.acp.menu.link.cms.page.list{/lang}</span></a></li>

			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='cms' controller='PageAdd'}{/link}{else}{link application='cms' controller='PageEdit' id=$pageID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<dl{if $errorField == 'pageObjectTypeID'} class="formError"{/if}>
			<dt><label for="pageObjectTypeID">{lang}cms.page.type{/lang}</label></dt>
			<dd>
				<select id="pageObjectTypeID" name="pageObjectTypeID" required="required">
					{foreach from=$availablePageTypes item=pageType}
						<option value="{$pageType->objectTypeID}"{if $pageObjectTypeID == $pageType->objectTypeID} selected="selected"{/if}>{lang}cms.acp.page.type.{$pageType->objectType}{/lang}</option>
					{/foreach}
				</select>
				{if $errorField == 'pageObjectTypeID'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{else}
							{lang}cms.acp.page.type.error.{@$errorType}{/lang}
						{/if}
					</small>
				{/if}
			</dd>
		</dl>
	</div>
	
	<div class="tabMenuContainer" data-active="{$activeTabMenuItem}" data-store="activeTabMenuItem">
		<nav class="tabMenu">
			<ul>
				<li><a href="{@$__wcf->getAnchor('general')}">{lang}cms.acp.page.general{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('specific')}">{lang}cms.acp.page.specific{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('display')}">{lang}cms.acp.page.display{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('userPermissions')}">{lang}cms.acp.page.userPermissions{/lang}</a></li>
				{event name='tabMenuTabs'}
			</ul>
		</nav>

		<div id="general" class="container containerPadding tabMenuContent">
			<fieldset>
				<legend>{lang}wcf.global.form.data{/lang}</legend>
				
				<dl{if $errorField == 'title'} class="formError"{/if}>
					<dt><label for="title">{lang}wcf.global.title{/lang}</label></dt>
					<dd>
						<input type="text" id="title" name="title" value="{$i18nPlainValues['title']}" class="long" required="required" />
						{if $errorField == 'title'}
							<small class="innerError">
								{if $errorType == 'empty' || $errorType == 'multilingual'}
									{lang}wcf.global.form.error.multilingual{/lang}
								{else}
									{lang}cms.acp.page.title.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}

						{include file='multipleLanguageInputJavascript' elementIdentifier='title' forceSelection=false}
					</dd>
				</dl>

				<dl{if $errorField == 'alias'} class="formError"{/if}>
					<dt><label for="alias">{lang}cms.acp.page.alias{/lang}</label></dt>
					<dd>
						<input type="text" id="alias" name="alias" value="{$alias}" class="long" />
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
						<small class="jsAliasPreview"></small>
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

				{if $action == 'add'}
					<dl>
						<dt class="reversed"><label for="createMenuItem">{lang}cms.acp.page.general.createMenuItem{/lang}</label></dt>
						<dd>
							<input type="checkbox" id="createMenuItem" name="createMenuItem"{if $createMenuItem} checked="checked"{/if} />
							<small>{lang}cms.acp.page.general.createMenuItem.description{/lang}</small>
						</dd>
					</dl>
				{/if}

				{event name='dataFields'}
			</fieldset>

			<fieldset>
				<legend>{lang}cms.acp.page.meta{/lang}</legend>

				<dl{if $errorField == 'metaDescription'} class="formError"{/if}>
					<dt><label for="metaDescription">{lang}cms.acp.page.meta.description{/lang}</label></dt>
					<dd>
						<textarea id="metaDescription" name="metaDescription" rows="5" cols="40" class="long">{$i18nPlainValues['metaDescription']}</textarea>
						<small>{lang}cms.acp.page.meta.description.description{/lang}</small>
						{if $errorField == 'metaDescription'}
							<small class="innerError">
								{lang}cms.acp.page.meta.description.error.{@$errorType}{/lang}
							</small>
						{/if}

						{include file='multipleLanguageInputJavascript' elementIdentifier='metaDescription' forceSelection=false}
					</dd>
				</dl>

				<dl{if $errorField == 'metaKeywords'} class="formError"{/if}>
					<dt><label for="metaKeywords">{lang}cms.acp.page.meta.keywords{/lang}</label></dt>
					<dd>
						<input type="text" id="metaKeywords" name="metaKeywords" value="{$i18nPlainValues['metaKeywords']}" class="long" />
						{if $errorField == 'metaKeywords'}
							<small class="innerError">
								{lang}cms.acp.page.meta.keywords.error.{@$errorType}{/lang}
							</small>
						{/if}

						{include file='multipleLanguageInputJavascript' elementIdentifier='metaKeywords' forceSelection=false}
					</dd>
				</dl>

				<dl>
					<dt class="reversed"><label for="allowIndexing">{lang}cms.acp.page.meta.allowIndexing{/lang}</label></dt>
					<dd>
						<input type="checkbox" id="allowIndexing" name="allowIndexing"{if $allowIndexing} checked="checked"{/if} />
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
										<option data-alias="{$node->getAlias()}"{if $node->pageID == $parentID} selected="selected"{/if} value="{@$node->pageID}">{@"&nbsp;&nbsp;&nbsp;&nbsp;"|str_repeat:$pageList->getDepth()}{$node->getTitle()}</option>
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
				<legend>{lang}cms.acp.page.publication{/lang}</legend>

				<dl>
					<dt class="reversed"><label for="enableDelayedPublication">{lang}cms.acp.page.publication.enableDelayedPublication{/lang}</label></dt>
					<dd>
						<input type="checkbox" id="enableDelayedPublication" name="enableDelayedPublication" value="1"{if $enableDelayedPublication} checked="checked"{/if} data-toggle-container="publicationDateContainer" />
					</dd>
				</dl>

				<dl id="publicationDateContainer"{if $errorField == 'publicationDate'} class="formError"{/if}{if !$enableDelayedPublication} style="display: none"{/if}>
					<dt><label for="publicationDate">{lang}cms.acp.page.publication.publicationDate{/lang}</label></dt>
					<dd>
						<input type="datetime" id="publicationDate" name="publicationDate" class="medium" value="{$publicationDate}" />
						{if $errorField == 'publicationDate'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}cms.acp.page.publication.publicationDate.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>

				<dl>
					<dt class="reversed"><label for="enableDelayedDeactivation">{lang}cms.acp.page.publication.enableDelayedDeactivation{/lang}</label></dt>
					<dd>
						<input type="checkbox" id="enableDelayedDeactivation" name="enableDelayedDeactivation" value="1"{if $enableDelayedDeactivation} checked="checked"{/if} data-toggle-container="deactivationDateContainer" />
					</dd>
				</dl>

				<dl id="deactivationDateContainer"{if $errorField == 'deactivationDate'} class="formError"{/if}{if !$enableDelayedDeactivation} style="display: none"{/if}>
					<dt><label for="deactivationDate">{lang}cms.acp.page.publication.deactivationDate{/lang}</label></dt>
					<dd>
						<input type="datetime" id="deactivationDate" name="deactivationDate" class="medium" value="{$deactivationDate}" />
						{if $errorField == 'deactivationDate'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}cms.acp.page.publication.deactivationDate.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>

				{event name='publicationFields'}
			</fieldset>

			<fieldset>
				<legend>{lang}cms.acp.page.settings{/lang}</legend>

				<dl{if $errorField == 'menuItemID'} class="formError"{/if}{if $action == 'add' && $createMenuItem} style="display: none"{/if}>
					<dt><label for="menuItemID">{lang}cms.acp.page.settings.menuItemID{/lang}</label></dt>
					<dd>
						<select id="menuItemID" name="menuItemID">
							<option value="0">{lang}wcf.global.noSelection{/lang}</option>
							{foreach from=$menuItems item=menuItem}
								<option value="{@$menuItem->menuItemID}"{if $menuItemID == $menuItem->menuItemID} selected="selected"{/if}>{$menuItem->menuItem|language}</option>
								{foreach from=$menuItem item=childMenuItem}
									<option value="{@$childMenuItem->menuItemID}"{if $menuItemID == $childMenuItem->menuItemID} selected="selected"{/if}>&nbsp;&nbsp;&nbsp;&nbsp;{$childMenuItem->menuItem|language}</option>
								{/foreach}
							{/foreach}
						</select>
						{if $errorField == 'menuItemID'}
							<small class="innerError">{lang}cms.acp.page.settings.menuItemID.error.{@$errorType}{/lang}</small>
						{/if}
						<small>{lang}cms.acp.page.settings.menuItemID.description{/lang}</small>
					</dd>
				</dl>

				<dl{if $errorField == 'availableDuringOfflineMode'} class="formError"{/if}>
					<dt class="reversed"><label for="availableDuringOfflineMode">{lang}cms.acp.page.settings.availableDuringOfflineMode{/lang}</label></dt>
					<dd>
						<input type="checkbox" name="availableDuringOfflineMode" id="availableDuringOfflineMode" value="1"{if $availableDuringOfflineMode} checked="checked"{/if} />
					</dd>
				</dl>

				<dl>
					<dt class="reversed"><label for="allowSubscribing">{lang}cms.acp.page.settings.allowSubscribing{/lang}</label></dt>
					<dd>
						<input type="checkbox" id="allowSubscribing" name="allowSubscribing"{if $allowSubscribing} checked="checked"{/if} />
						<small>{lang}cms.acp.page.settings.allowSubscribing.description{/lang}</small>
					</dd>
				</dl>

				{event name='settingsFields'}
			</fieldset>

			{event name='fieldsets'}
		</div>

		<div id="specific" class="container containerPadding tabMenuContent">
			{@$pageForm}
		</div>

		<div id="display" class="container containerPadding tabMenuContent">
			<fieldset>
				<legend>{lang}cms.acp.page.display{/lang}</legend>

				<dl>
					<dt><label for="styleID">{lang}cms.acp.page.styleID{/lang}</label></dt>
					<dd>
						<select id="styleID" name="styleID">
							<option value="0">{lang}wcf.global.noSelection{/lang}</option>
							{htmlOptions options=$availableStyles selected=$styleID}
						</select>
						<small>{lang}cms.acp.page.styleID.description{/lang}</small>
					</dd>
				</dl>

				{hascontent}
					<dl>
						<dt>{lang}cms.acp.page.stylesheets{/lang}</dt>
						<dd>
							{content}
								{htmlCheckboxes name='stylesheetIDs' options=$stylesheetList selected=$stylesheetIDs}
							{/content}
							{if $errorField == 'stylesheets'}
								<small class="innerError">
									{lang}cms.acp.page.stylesheets.error.{@$errorType}{/lang}
								</small>
							{/if}
							<small>{lang}cms.acp.page.stylesheets.description{/lang}</small>
						</dd>
					</dl>
				{/hascontent}

				{event name='displayFields'}
			</fieldset>

			<fieldset>
				<legend>{lang}cms.acp.page.display.settings{/lang}</legend>

				<dl{if $errorField == 'sidebarOrientation'} class="formError"{/if}>
					<dt><label for="sidebarOrientation">{lang}cms.acp.page.display.settings.sidebarOrientation{/lang}</label></dt>
					<dd>
						<select id="sidebarOrientation" name="sidebarOrientation">
							<option value="right"{if $sidebarOrientation =="right"} selected="selected"{/if}>{lang}cms.acp.page.display.settings.sidebarOrientation.right{/lang}</option>
							<option value="left"{if $sidebarOrientation =="left"} selected="selected"{/if}>{lang}cms.acp.page.display.settings.sidebarOrientation.left{/lang}</option>
						</select>
					</dd>
				</dl>

				{event name='displaySettingsFields'}
			</fieldset>

			{event name='afterDisplayFieldsets'}
		</div>

		<div id="userPermissions" class="container containerPadding tabMenuContent">
			<fieldset>
				<legend>{lang}wcf.acl.permissions{/lang}</legend>

				<dl id="userPermissionsContainer" class="wide">
					<dd></dd>
				</dl>
			</fieldset>

			{event name='afterPermissionsFieldsets'}
		</div>

		{event name='tabMenuContents'}
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
