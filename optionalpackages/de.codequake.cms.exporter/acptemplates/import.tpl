{capture assign='pageTitle'}{lang}cms.acp.restore.import{/lang}{/capture}
{include file='header'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.restore.import{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
<p class="success">{lang}wcf.global.success{/lang}</p>
{/if}

<p class="info">{lang}cms.acp.restore.info{/lang}</p>

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a class="button jsTooltip" href="{link controller='RestoreList' application='cms'}{/link}" title="{lang}cms.acp.restore.list{/lang}"><span class="icon icon16 icon-list"></span> <span>{lang}cms.acp.restore.list{/lang}</span></a></li>
		</ul>
	</nav>
</div>

<form method="post" enctype="multipart/form-data" action="{link controller='Import' application='cms'}{/link}">
    <div class="container containerPadding marginTop">
        <fieldset>
            <legend>{lang}cms.acp.restore.tar{/lang}</legend>
            <dl{if $errorField == 'file'} class="formError"{/if}>
                <dt><label for="file">{lang}cms.acp.restore.tar{/lang}</label></dt>
                <dd>
                    <input type="file" name="file" id="file"  required="required"/>
                    {if $errorField == 'file'}
                        <small class="innerError">
                              {lang}cms.acp.restore.import.{$errorType}{/lang}
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