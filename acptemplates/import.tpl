{capture assign='pageTitle'}{lang}cms.acp.page.import{/lang}{/capture}
{include file='header'}

<header class="boxHeadline">
	<h1>{lang}cms.acp.page.import{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
<p class="success">{lang}wcf.global.success{/lang}</p>
{/if}

<p class="info">{lang}cms.acp.page.import.info{/lang}</p>

<form method="post" enctype="multipart/form-data" action="{link controller='CMSImport' application='cms'}{/link}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}cms.acp.page.import.tar{/lang}</legend>
			<dl{if $errorField == 'file'} class="formError"{/if}>
				<dt><label for="file">{lang}cms.acp.page.import.tar{/lang}</label></dt>
				<dd>
					<input type="file" name="file" id="file"  required="required"/>
					{if $errorField == 'file'}
						<small class="innerError">
							  {lang}cms.acp.page.import.{$errorType}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
	   </fieldset>
	</div>
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>
{include file='footer'}
