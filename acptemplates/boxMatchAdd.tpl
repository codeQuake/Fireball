{include file='header' pageTitle='cms.acp.boxmatch.'|concat:$action}

<header class="boxHeadline">
	<h1>{lang}cms.acp.boxmatch.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

<form method="post" action="{link application='cms' controller='BoxMatchAdd'}{/link}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.global.form.data{/lang}</legend>

			<dl{if $errorField == 'contentID'} class="formError"{/if}>
				<dt><label for="contentID">{lang}cms.boxmatch.content{/lang}</label></dt>
				<dd>
					<select id="contentID" name="contentID" required="required">
						{foreach from=$contentNodeTree item=contentNode}
							<option value="{$contentNode->contentID}"{if $contentID == $contentNode->contentID} selected="selected"{/if}>{section name=i loop=$contentNodeTree->getIterator()->getDepth()}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$contentNode->getTitle()}</option>
						{/foreach}
					</select>

					{if $errorField == 'contentID'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}cms.acp.boxmatch.content.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>

			<dl{if $errorField == 'position'} class="formError"{/if}>
				<dt><label for="position">{lang}cms.boxmatch.position{/lang}</label></dt>
				<dd>
					<select id="position" name="position" required="required">
						<option value="content"{if $position == 'content'} selected="selected"{/if}>content</option>
						<option value="sidebar"{if $position == 'sidebar'} selected="selected"{/if}>sidebar</option>
					</select>

					{if $errorField == 'position'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}cms.acp.boxmatch.position.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
		</fieldset>

		{event name='fieldsets'}
	</div>

	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}
