<dl>
	<dt><label for="contentData[latitude]">{lang}cms.acp.content.type.de.codequake.cms.content.type.googlemaps.latitude{/lang}</label></dt>
	<dd>
		<input type="text" id="contentData[latitude]" name="contentData[latitude]" value="{if !$contentData['latitude']|empty}{$contentData['latitude']}{/if}" required="required" class="medium" /> N
		{if $errorField == 'latitude'}
			<small class="innerError">
				{if $errorType == 'empty'}
					{lang}wcf.global.form.error.empty{/lang}
				{else}
					{lang}cms.acp.content.type.de.codequake.cms.content.type.googlemaps.latitude.error.{@$errorType}{/lang}
				{/if}
			</small>
		{/if}
	</dd>
</dl>

<dl>
	<dt><label for="contentData[longitude]">{lang}cms.acp.content.type.de.codequake.cms.content.type.googlemaps.longitude{/lang}</label></dt>
	<dd>
		<input type="text" id="contentData[longitude]" name="contentData[longitude]" value="{if !$contentData['longitude']|empty}{$contentData['longitude']}{/if}" required="required" class="medium" /> E
		{if $errorField == 'longitude'}
			<small class="innerError">
				{if $errorType == 'empty'}
					{lang}wcf.global.form.error.empty{/lang}
				{else}
					{lang}cms.acp.content.type.de.codequake.cms.content.type.googlemaps.longitude.error.{@$errorType}{/lang}
				{/if}
			</small>
		{/if}
	</dd>
</dl>
