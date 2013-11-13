{capture assign='pageTitle'}{lang}cms.acp.module.{@$action}{/lang}{/capture}
{include file='header'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.module.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<div class="contentNavigation">
    <nav>
        <ul>
            <li><a href="{link application='cms' controller='ModuleList'}{/link}" title="{lang}cms.acp.menu.link.cms.module.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}cms.acp.menu.link.cms.module.list{/lang}</span></a></li>
            {event name='contentNavigationButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='cms' controller='ModuleAdd'}{/link}{else}{link application='cms' controller='ModuleEdit' id=$moduleID}{/link}{/if}">
    <div class="container marginTop containerPadding">
        <fieldset>
			<legend>{lang}cms.acp.module.general{/lang}</legend>
				<dl>
					<dt><label for="title">{lang}cms.acp.module.title{/lang}</label></dt>
					<dd><input type="text" name="title" id="title" required="required" value="{$title}"/></dd>
				</dl>
		</fieldset>
		<fieldset class="marginTop">
				<legend>{lang}cms.acp.module.php{/lang}</legend>
				
				<dl class="wide">
					<dd>
						<textarea id="php" rows="20" cols="40" name="php">{$phpCode}</textarea>
						<small>{lang}cms.acp.module.php.description{/lang}</small>
					</dd>
				</dl>
		</fieldset>

        <fieldset class="marginTop">
				<legend>{lang}cms.acp.module.tpl{/lang}</legend>
				
				<dl class="wide">
					<dd>
						<textarea id="tpl" rows="20" cols="40" name="tpl">{$tplCode}</textarea>
						<small>{lang}cms.acp.module.tpl.description{/lang}</small>
					</dd>
				</dl>
		</fieldset>
    </div>
    {include file='codemirror' codemirrorMode='php' codemirrorSelector='#php'}
    {include file='codemirror' codemirrorMode='smarty' codemirrorSelector='#tpl'}
<div class="formSubmit">
		    <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
			 {@SECURITY_TOKEN_INPUT_TAG}
	    </div>
</form>

{include file='footer'}