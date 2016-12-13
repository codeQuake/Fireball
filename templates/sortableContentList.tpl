<div class="ui-droppable" data-position="{$position}"></div>

<div class="sortableListContainer sortableContentList" id="sortableContentList{$position|ucfirst}">
	<ol class="sortableList ui-sortable" data-object-id="0">
		{foreach from=$contentNodeTree item=contentNode}
		<li style="margin-top: 10px; padding-bottom: 10px;" class="jsContentRow sortableNode jsCollapsibleCategory ui-droppable {$contentNode->getCSSClasses()}" id="cmsContent{$contentNode->contentID}"
		    data-object-id="{$contentNode->contentID}"
		    data-depth="{$contentNodeTree->getDepth()}"
		    data-content-type="{$contentNode->getTypeName()}"
		    data-children="{$contentNode->count()}"
		    data-position="{$position}">
			<ul class="buttonList">
				<li><span class="icon icon16 fa-times jsDeleteButton jsTooltip pointer" data-object-id="{$contentNode->contentID}" title="{lang}wcf.global.button.delete{/lang}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span></li>
				<li><span class="icon icon16 fa-pencil jsEditButton jsTooltip pointer" data-object-id="{$contentNode->contentID}" title="{lang}wcf.global.button.edit{/lang}"></span></li>
				<li><span class="icon icon16 fa{if !$contentNode->isDisabled}-check{/if}-square-o jsToggleButton jsTooltip pointer" data-object-id="{$contentNode->contentID}" title="{lang}wcf.global.button.{if !$contentNode->isDisabled}disable{else}enable{/if}{/lang}"></span></li>
			</ul>

			<span class="{if $position == 'sidebar'}boxTitle{else}sectionTitle{/if}">{$contentNode->getTitle()} <small>({$contentNode->getTypeName()})</small></span>

			{if $position == 'sidebar'}
				<div class="boxContent">{@$contentNode->getOutput(true)|language}</div>
			{else}
				{@$contentNode->getOutput(true)|language}
			{/if}

			<ol class="sortableList ui-sortable" style="margin-left: 5px; margin-right: 5px;"
			    data-object-id="{$contentNode->contentID}"
			    data-position="{$position}">{if !$contentNode->hasChildren()}</ol></li>{/if}
			{if !$contentNode->hasChildren() && $contentNode->isLastSibling()}
				{@"</ol></li>"|str_repeat:$contentNode->getOpenParentNodes()}
			{/if}
		{/foreach}
	</ol>
</div>

<script data-relocate="true">
	$(function() {
		require(['WoltLabSuite/Core/Controller/Clipboard', 'Language'], function(ControllerClipboard, Language) {
			var actionObjects = { };

			var deleteAction = new WCF.Action.NestedDelete('cms\\data\\content\\ContentAction', '#sortableContentList{$position|ucfirst} .jsContentRow');
			deleteAction.setCallback(ControllerClipboard.reload.bind(ControllerClipboard));

			var toggleAction = new WCF.Action.Toggle('cms\\data\\content\\ContentAction', '#sortableContentList{$position|ucfirst} .jsContentRow');

			actionObjects['de.codequake.cms.content'] = { };
			actionObjects['de.codequake.cms.content']['disable'] = actionObjects['de.codequake.cms.content']['enable'] = toggleAction;
			actionObjects['de.codequake.cms.content']['delete'] = deleteAction;

			WCF.Clipboard.init('cms\\page\\AbstractPagePage', 0, actionObjects);
		});
	});
</script>
