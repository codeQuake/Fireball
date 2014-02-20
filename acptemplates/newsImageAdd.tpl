{capture assign='pageTitle'}{lang}cms.acp.news.image.{$action}{/lang}{/capture}
{include file='header'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.news.image.{$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
<p class="success">{lang}wcf.global.success.add{/lang}</p>
{/if}

<form method="post" enctype="multipart/form-data" action="{if $action == 'add'}{link controller='NewsImageAdd' application='cms'}{/link}{else}{link controller='NewsImageEdit' application='cms' object=$newsImage}{/link}{/if}">
    <div class="container containerPadding marginTop">
        <fieldset>
            <legend>{lang}cms.acp.news.image.data{/lang}</legend>
             <dl>
                <dt><label for="title">{lang}cms.acp.news.image.title{/lang}</label></dt>
                <dd>
                    <input type="text"  id="title" name="title" value="{$title}"/>
                </dd>
            </dl>
            <dl{if $errorField == 'image'} class="formError"{/if}>
                <dt><label for="image">{lang}cms.acp.news.image{/lang}</label></dt>
                <dd>
                    <input type="file" name="image" id="image"  required="required"/>
                    {if $errorField == 'image'}
                        <small class="innerError">
                              {lang}cms.acp.news.image.{$errorType}{/lang}
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