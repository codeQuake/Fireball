{capture assign='pageTitle'}{lang}wcf.acp.cms.content.{@$action}{/lang}{/capture}
{include file='header'}

{include file='multipleLanguageInputJavascript' elementIdentifier='subject' forceSelection=false}
{include file='multipleLanguageInputJavascript' elementIdentifier='text' forceSelection=false}
<header class="boxHeadline">
	<h1>{lang}wcf.acp.cms.content.{@$action}{/lang}</h1>
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
			<li><a href="{link application='cms' controller='ContentList'}{/link}" title="{lang}cms.acp.menu.link.cms.content.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}cms.acp.menu.link.cms.content.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='cms' controller='ContentAdd'}{/link}{else}{link application='cms' controller='ContentEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop shadow">
		<fieldset>
			<legend>{lang}wcf.acp.cms.content.general{/lang}</legend>
			<dl{if $errorField == 'subject'} class="formError"{/if}>
				<dt><label for="subject">{lang}wcf.acp.cms.content.title{/lang}</label></dt>
				<dd>
					<input type="text" id="subject" name="subject" value="{@$i18nPlainValues['subject']}" class="long" required="required" placeholder="{lang}wcf.acp.cms.content.title.placeholder{/lang}" pattern=".{literal}{{/literal}4,{literal}}{/literal}" />
					{if $errorField == 'subject'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.cms.content.title.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			
			<dl{if $errorField == 'text'} class="formError"{/if}>
				<dt><label for="text">{lang}wcf.acp.cms.content.text{/lang}</label></dt>
				<dd>
					<textarea id="text" name="text" rows="15" cols="40" class="long">{@$i18nPlainValues['text']}</textarea>
					
					{if $errorField == 'text'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.cms.content.text.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
					{include file='messageFormTabs' wysiwygContainerID='text'}
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
		{if $contentID|isset}<input type="hidden" name="id" value="{@$contentID}" />{/if}
	</div>
</form>

{include file='footer'}
{include file='wysiwyg'}