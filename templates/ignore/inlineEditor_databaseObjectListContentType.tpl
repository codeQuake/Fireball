<dl>
	<dt><label for="contentData[objectListClassname]">{lang}cms.acp.content.type.de.codequake.cms.content.type.databaseobjectlist.objectListClassname{/lang}</label></dt>
	<dd>
		<input type="text" id="contentData[objectListClassname]" name="contentData[objectListClassname]" value="{if !$contentData['objectListClassname']|empty}{$contentData['objectListClassname']}{/if}" required="required" class="long" />
		{if $errorField == 'objectListClassname'}
			<small class="innerError">
				{if $errorType == 'empty'}
					{lang}wcf.global.form.error.empty{/lang}
				{else}
					{lang}cms.acp.content.type.de.codequake.cms.content.type.databaseobjectlist.objectListClassname.error.{@$errorType}{/lang}
				{/if}
			</small>
		{/if}
	</dd>
</dl>

<dl>
	<dt><label for="contentData[maxItems]">{lang}cms.acp.content.type.de.codequake.cms.content.type.databaseobjectlist.maxItems{/lang}</label></dt>
	<dd>
		<input type="number" id="contentData[maxItems]" name="contentData[maxItems]" value="{if !$contentData['maxItems']|empty}{$contentData['maxItems']}{/if}" required="required" min="0" max="100" class="medium" />
		{if $errorField == 'maxItems'}
			<small class="innerError">
				{if $errorType == 'empty'}
					{lang}wcf.global.form.error.empty{/lang}
				{else}
					{lang}cms.acp.content.type.de.codequake.cms.content.type.databaseobjectlist.maxItems.error.{@$errorType}{/lang}
				{/if}
			</small>
		{/if}
	</dd>
</dl>

{if !$additionalTemplate|empty}
	{include file=$additionalTemplate application='cms'}
{/if}
