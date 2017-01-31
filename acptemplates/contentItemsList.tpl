{hascontent}
	<section id="contentList{$position|ucfirst}" class="sortableListContainer">
		<ol class="contentList{$position|ucfirst} sortableList" data-object-id="0">
			{content}
				{if !$contentList[$position]|empty}
					{foreach from=$contentList[$position] item=content}
						<li class="jsClipboardObject jsContentRow sortableNode" data-object-id="{$content->contentID}">
							<span class="sortableNodeLabel">
								<span class="title">
									<input type="checkbox" class="jsClipboardItem" data-object-id="{@$content->contentID}" />
									<span class="pointer collapsibleButton icon icon16 {$content->getIcon()}"></span>
									<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}position={$position}{/link}">{@$content->getTitle()}</a> - <small>{lang}cms.acp.content.type.{$content->getTypeName()}{/lang}</small>
								</span>
								<span class="statusDisplay buttons">
									<a href="{link controller='ContentEdit' application='cms' object=$content objectType=$content->getTypeName()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
									<span class="icon icon16 fa-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$content->contentID}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span>
									<span class="icon icon16 fa-{if !$content->isDisabled}check-{/if}square-o jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if !$content->isDisabled}disable{else}enable{/if}{/lang}" data-object-id="{@$content->contentID}"></span>
									<span class="icon icon16 fa-plus jsContentAddButton jsTooltip pointer" title="{lang}cms.acp.content.add{/lang}" data-object-id="{@$content->pageID}" data-position="{$position}" data-parent-id="{$content->contentID}"></span>
									<span class="icon icon16 fa-copy jsCopyButton jsTooltip pointer" title="{lang}cms.acp.content.copy{/lang}" data-object-id="{@$content->contentID}"></span>

									{event name='itemButtons'}
								</span>
							</span>

							<ol class="contentList{$position|ucfirst} sortableList" data-object-id="{@$content->contentID}">{if !$content->hasChildren()}</ol></li>{/if}
							{if !$content->hasChildren() && $content->isLastSibling()}
								{@"</ol></li>"|str_repeat:$content->getOpenParentNodes()}
							{/if}
					{/foreach}
				{/if}
			{/content}
		</ol>
	</section>

	<div class="formSubmit">
		<button class="button buttonPrimary" data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
		<button class="button jsContentAddButton" data-object-id="{$page->pageID}" data-position="{$position}"><span class="icon icon16 fa-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></button>
	</div>

{hascontentelse}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
	<div class="formSubmit">
		<button class="button jsContentAddButton" data-object-id="{$page->pageID}" data-position="{$position}"><span class="icon icon16 fa-plus"></span> <span>{lang}cms.acp.content.add{/lang}</span></button>
	</div>
{/hascontent}
