{include file='header' pageTitle='cms.acp.page.import'}

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
			<legend>{lang}cms.acp.page.import{/lang}</legend>

			<dl{if $errorField == 'file'} class="formError"{/if}>
				<dt><label for="file">{lang}cms.acp.page.import.upload{/lang}</label></dt>
				<dd>
					<input type="file" name="file" id="file"  required="required"/>
					{if $errorField == 'file'}
						<small class="innerError">
							{lang}cms.acp.page.import.{$errorType}{/lang}
						</small>
					{/if}
					<small>{lang}cms.acp.page.import.upload.description{/lang}</small>
				</dd>
			</dl>
		</fieldset>
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

<header class="boxHeadline">
	<h1>{lang}cms.acp.page.export{/lang}</h1>
</header>

<div class="container containerPadding marginTop">
	<fieldset>
		<legend>{lang}cms.acp.page.export{/lang}</legend>

		<dl id="pageExportDiv">
			<dt><label>{lang}cms.acp.page.export.download{/lang}</label></dt>
			<dd>
				<p><a href="{link application='cms' controller='CMSExport'}{/link}" id="pageExport" class="button">{lang}cms.acp.page.export{/lang}</a></p>
				<small>{lang}cms.acp.page.export.download.description{/lang}</small>
			</dd>
		</dl>

		{event name='exportFields'}
	</fieldset>

	{event name='exportFieldsets'}
</div>

{include file='footer'}
