{capture assign='pageTitle'}{lang}cms.acp.file.management{/lang}{/capture}
{include file='header'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.file.management{/lang}</h1>
</header>

<script data-relocate="true">
    //<![CDATA[
    $(function () {
        new WCF.Action.Delete('cms\\data\\file\\FileAction', '.jsFileRow');
    });
    //]]>
</script>

{if $errorField}
<p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $success|isset}
<p class="success">{lang}wcf.global.success.add{/lang}</p>
{/if}

<form method="post" enctype="multipart/form-data" action="{link controller='FileManagement' application='cms'}{/link}">
    <div class="container containerPadding marginTop">
        <fieldset>
            <legend>{lang}cms.acp.file.file{/lang}</legend>
            <dl{if $errorField == 'file'} class="formError"{/if}>
                <dt><label for="filed">{lang}cms.acp.file.file{/lang}</label></dt>
                <dd>
                    <input type="file" name="file" id="file"  required="required"/>
                    {if $errorField == 'file'}
                        <small class="innerError">
                              {lang}cms.acp.file.error.{$errorType}{/lang}
                        </small>
                    {/if}
                </dd>
            </dl>
        </fieldset>
    </div>

    <div class="formSubmit">
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
    </div>
</form>
{if $fileList|count}
    <div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}cms.acp.file.list{/lang} <span class="badge badgeInverse">{#$fileList}</span></h2>
		</header>
        <table class="table">
            <thead>
                <th class="columnID columnPageID" colspan="2">{lang}wcf.global.objectID{/lang}</th>
			    <th class="columnTitle columnFile">{lang}cms.acp.file.title{/lang}</th>
                <th class="columnType">{lang}cms.acp.file.type{/lang}</th>
                <th class="downloads">{lang}cms.acp.file.downloads{/lang}</th>
			    {event name='columnHeads'}
            </thead>
            <tbody>
                {foreach from=$fileList item=file}
                    <tr class="jsFileRow">
                        <td class="columnIcon">
                            <span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$file->fileID}" data-confirm-message="{lang}cms.acp.file.delete.sure{/lang}"></span>
                        </td>
                        <td class="columnID">{@$file->fileID}</td> 
                        <td class="columnTitle columnFile">{$file->title|language}</td>
                        <td class="columnType">{$file->type}</td>
                        <td class="columnDownloads">{#$file->downloads}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
{/if}

{include file='footer'}