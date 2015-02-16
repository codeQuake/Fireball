<dl>
	<dt><label for="contentData[url]">{lang}cms.acp.content.type.de.codequake.cms.content.type.rss.url{/lang}</label></dt>
	<dd>
		<input name="contentData[url]" id="contentData[url]" type="text" value="{if $contentData['url']|isset}{$contentData['url']}{/if}"  class="long" />
    <small>{lang}cms.acp.content.type.de.codequake.cms.content.type.rss.url.description{/lang}</small>
  </dd>
</dl>

<dl>
  <dt>
    <label for="contentData[limit]">{lang}cms.acp.content.type.de.codequake.cms.content.type.rss.limit{/lang}</label>
  </dt>
  <dd>
    <input name="contentData[limit]" id="contentData[limit]" type="number" value="{if $contentData['limit']|isset}{$contentData['limit']}{else}5{/if}" />
  </dd>
</dl>
