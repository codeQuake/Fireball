<div class="ui-droppable"></div>

<div class="sortableListContainer sortableContentList" id="sortableContentList{$position|ucfirst}">
	<ol class="sortableList" data-object-id="0">
		{foreach from=$contentNodeTree item=content}
		<li style="margin-top: 10px; padding-bottom: 10px;" class="sortableNode jsCollapsibleCategory ui-droppable {$content->getCSSClasses()}" id="cmsContent{$content->contentID}" data-object-id="{$content->contentID}" data-depth="{$contentNodeTree->getDepth()}" data-content-type="{$content->getTypeName()}">
			<ul class="buttonList">
				<li><span class="icon icon16 fa-times pointer"></span></li>
				<li><span class="icon icon16 fa-pencil pointer"></span></li>
				<li><span class="icon icon16 fa{if !$content->isDisabled}-check{/if}-square-o pointer"></span></li>
			</ul>

		{@$content->getOutput()|language}

			<ol class="sortableList" data-object-id="' + $(this).attr('id').replace('cmsContent', '') + '" style="margin-left: 5px; margin-right: 5px;">{if !$content->hasChildren()}</ol></li>{/if}
			{if !$content->hasChildren() && $content->isLastSibling()}
				{@"</ol></li>"|str_repeat:$content->getOpenParentNodes()}
			{/if}
		{/foreach}
	</ol>
</div>
