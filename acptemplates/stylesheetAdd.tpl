{capture assign='pageTitle'}{lang}cms.acp.stylesheet.{@$action}{/lang}{/capture}
{include file='header'}

<header class="boxHeadline">
	<h1>{lang}cms.acp.stylesheet.{$action}{/lang}</h1>
</header>

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<form method="post" action="{link controller='StyleSheetAdd' application='cms'}{/link}">
    <div class="container containerPadding">  
        <fieldset>
			<legend>{lang}cms.acp.stylesheet.general{/lang}</legend>
				<dl>
					<dt><label for="title">{lang}cms.acp.stylesheet.title{/lang}</label></dt>
					<dd><input type="text" name="title" id="title" required="required" value="{$title}"/></dd>
				</dl>
		</fieldset>
		<fieldset class="marginTop">
				<legend>{lang}cms.acp.stylesheet.less{/lang}</legend>
				
				<dl class="wide">
					<dd>
						<textarea id="less" rows="20" cols="40" name="less">{$less}</textarea>
						<small>{lang}cms.acp.stylesheet.less.description{/lang}</small>
					</dd>
				</dl>
		</fieldset>
    </div>  
    {include file='codemirror' codemirrorMode='less' codemirrorSelector='#less'}
    <div class="formSubmit">
		    <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
	    </div>
</form>

{include file='footer'}