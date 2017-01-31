{include file='header' pageTitle='cms.acp.content.list'}

<script data-relocate="true" src="{@$__wcf->getPath('cms')}acp/js/Fireball.ACP.js?v={@LAST_UPDATE_TIME}"></script>
<script data-relocate="true">
	require(['Language'], function(Language) {
		Language.addObject({
			'cms.acp.content.add': '{lang}cms.acp.content.add{/lang}',
			{foreach from=$objectTypeList item=type}
				'cms.acp.content.type.{$type->objectType}': '{lang}cms.acp.content.type.{$type->objectType}{/lang}',
			{/foreach}
			'cms.acp.content.type.content': '{lang}cms.acp.content.type.content{/lang}'
		});

		WCF.TabMenu.init();

		var deleteAction = new WCF.Action.NestedDelete('cms\\data\\content\\ContentAction', '.jsContentRow');
		var toggleAction = new WCF.Action.Toggle('cms\\data\\content\\ContentAction', '.jsContentRow', '> .sortableNodeLabel > .buttons > .jsToggleButton');

		var actionObjects = { };
		actionObjects['de.codequake.cms.content'] = { };
		actionObjects['de.codequake.cms.content']['disable'] = actionObjects['de.codequake.cms.content']['enable'] = toggleAction;
		actionObjects['de.codequake.cms.content']['delete'] = deleteAction;

		WCF.Clipboard.init('cms\\acp\\page\\ContentListPage', {@$hasMarkedItems}, actionObjects);

		{foreach from=availablePositions item=position}
			new WCF.Sortable.List('contentList{$position|ucfirst}', 'cms\\data\\content\\ContentAction');
		{/foreach}

		new Fireball.ACP.Content.AddDialog();

		new WCF.Action.SimpleProxy({
			action: 'copy',
			className: 'cms\\data\\content\\ContentAction',
			elements: $('.jsContentRow .jsCopyButton')
		}, {
			success: function() {
				window.location.reload();
			}
		});
	});
</script>

<header class="boxHeadline">
	<h1>{lang}cms.acp.content.list{/lang}</h1>
	<p>{lang includeSelf=true}cms.page.parents{/lang}</p>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			<li class="dropdown">
				<a class="button dropdownToggle"><span class="icon icon16 fa-sort"></span> <span>{lang}cms.acp.page.button.choose{/lang}</span></a>
				<div class="dropdownMenu">
					<ul class="scrollableDropdownMenu">
						{foreach from=$pageList item=node}
							<li{if $node->pageID == $pageID} class="active"{/if}><a href="{link application='cms' controller='ContentList' pageID=$node->pageID}{/link}">{@'&nbsp;&nbsp;&nbsp;&nbsp;'|str_repeat:$pageList->getDepth()}{$node->getTitle()}</a></li>
						{/foreach}
					</ul>
				</div>
			</li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $pageID}
	<div class="jsClipboardContainer tabMenuContainer" data-type="de.codequake.cms.content">
		<nav class="tabMenu">
			<ul>
				{foreach from=availablePositions item=position}
					<li><a href="{@$__wcf->getAnchor($position)}">{lang}cms.acp.content.position.position.{$position}{/lang}</a></li>
				{/foreach}

				{event name='tabMenuTabs'}
			</ul>
		</nav>

		{foreach from=availablePositions item=position}
			<div id="{$position}" class="tabMenuContent container containerPadding">
				{include file='contentItemsList' application='cms' position=$position}
			</div>
		{/foreach}

		{event name='tabMenuContents'}
	</div>

	<div class="contentNavigation">
		<nav class="jsClipboardEditor" data-types="[ 'de.codequake.cms.content' ]"></nav>
	</div>
{/if}

{include file='footer'}
