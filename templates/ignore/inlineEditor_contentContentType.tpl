<dl>
	<dt><label for="contentData[contentID]">{lang}cms.acp.content.type.de.codequake.cms.content.type.content{/lang}</label></dt>
	<dd>
		<select id="contentData[contentID]" name="contentData[contentID]" required>
			{foreach from=$contentNodeTree item=contentNode}
				<option value="{$contentNode->contentID}"{if $contentData['contentID']|isset && $contentData['contentID'] == $contentNode->contentID} selected="selected"{/if}{if $contentNode->getTypeName == 'de.codequake.cms.content.type.content' || (!$contentID|empty && !$contentData['contentID']|empty && $contentID == $contentData['contentID'])} disabled="disabled"{/if}>{section name=i loop=$contentNodeTree->getIterator()->getDepth()}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$contentNode->getTitle()}</option>
			{/foreach}
		</select>
	</dd>
</dl>
