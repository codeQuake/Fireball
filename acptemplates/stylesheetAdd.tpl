{include file='header' pageTitle='cms.acp.stylesheet.'|concat:$action}

<header class="boxHeadline">
	<h1>{lang}cms.acp.stylesheet.{$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			{if $action == 'edit' && $stylesheets|count > 1}
				<li class="dropdown">
					<a class="button dropdownToggle"><span class="icon icon16 icon-sort"></span> <span>{lang}cms.acp.stylesheet.button.choose{/lang}</span></a>
					<div class="dropdownMenu">
						<ul class="scrollableDropdownMenu">
							{foreach from=$stylesheets item=item}
								<li{if $item->stylesheetID == $stylesheetID} class="active"{/if}><a href="{link application='cms' controller='StylesheetEdit' id=$item->stylesheetID}{/link}">{$item->title}</a></li>
							{/foreach}
						</ul>
					</div>
				</li>
			{/if}
			<li><a href="{link application='cms' controller='StylesheetList'}{/link}" title="{lang}fireball.acp.menu.link.fireball.stylesheet.list{/lang}" class="button"><span class="icon icon16 icon-list"></span> <span>{lang}fireball.acp.menu.link.fireball.stylesheet.list{/lang}</span></a></li>

			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='StylesheetAdd' application='cms'}{/link}{else}{link controller='StylesheetEdit' id=$stylesheetID application='cms'}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.global.form.data{/lang}</legend>

			<dl>
				<dt><label for="title">{lang}wcf.global.title{/lang}</label></dt>
				<dd>
					<input type="text" name="title" id="title" required="required" value="{$title}" class="long" />
					{if $errorField == 'title'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}cms.acp.stylesheet.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>

			{event name='dataFields'}
		</fieldset>

		<fieldset class="marginTop">
			<legend>{lang}cms.acp.stylesheet.less{/lang}</legend>

			<dl class="wide">
				<dt></dt>
				<dd>
					<textarea id="less" rows="20" cols="40" name="less">{$less}</textarea>
					{if $errorField == 'less'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}cms.acp.stylesheet.less.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
					<small>{lang}cms.acp.stylesheet.less.description{/lang}</small>

					{include file='codemirror' codemirrorMode='less' codemirrorSelector='#less'}
				</dd>
			</dl>

			{event name='lessFields'}
		</fieldset>

		{event name='fieldsets'}
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
