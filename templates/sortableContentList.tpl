<div class="ui-droppable"></div>

<div class="sortableListContainer sortableContentList" id="sortableContentList{$position|ucfirst}">
	<ol class="sortableList" data-object-id="0">
		{assign var=oldDepth value=0}
		{foreach from=$contentNodeTree item=content}
			{section name=i loop=$oldDepth-$contentNodeTree->getDepth()}</ol></div>{/section}
			<li style="margin-top: 10px; padding-bottom: 10px;" class="sortableNode jsCollapsibleCategory ui-droppable {$content->getCSSClasses()}" id="cmsContent{$content->contentID}" data-object-id="{$content->contentID}" data-depth="{$oldDepth}" data-content-type="{$content->getTypeName()}">
				<ul class="buttonList">
					<li><span class="icon icon16 fa-times pointer"></span></li>
					<li><span class="icon icon16 fa-pencil pointer"></span></li>
					<li><span class="icon icon16 fa{if !$content->isDisabled}-check{/if}-square-o pointer"></span></li>
				</ul>

				{@$content->getOutput()|language}

				<ol class="sortableList" data-object-id="' + $(this).attr('id').replace('cmsContent', '') + '" style="margin-left: 5px; margin-right: 5px;">
				{assign var=oldDepth value=$contentNodeTree->getDepth()}
		{/foreach}
	</ol>
</div>
