{include file='header' pageTitle='cms.acp.boxmatch.list'}

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		new WCF.Action.NestedDelete('cms\\data\\content\\match\\ContentBoxMatchAction', '.jsMatchRow');
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}cms.acp.boxmatch.list{/lang}</h1>
	<p>{lang}cms.acp.boxmatch.list.description{/lang}</p>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='BoxMatchAdd' application='cms'}{/link}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}cms.acp.boxmatch.add{/lang}</span></a></li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $items}
	<div class="container marginTop tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th class="columnTitle">{lang}cms.acp.boxmatch.content{/lang}</th>
					<th class="columnText">{lang}cms.acp.boxmatch.page{/lang}</th>
					<th class="columnID">{lang}cms.acp.boxmatch.boxID{/lang}</th>
					<th class="columnText">{lang}cms.acp.boxmatch.position{/lang}</th>

					{event name='columnHeads'}
				</tr>
			</thead>
			<tbody>
				{foreach from=$objects item=match}
					<tr class="jsMatchRow">
						<td class="columnTitle">
							<span class="label badge gray idLabel">{#$match->contentID}</span>
							{$match->getContent()->getTitle()}
						</td>
						<td class="columnText">{$match->getContent()->getPage()->getTitle()}</td>
						<td class="columnID">
							{#$match->boxID}
						</td>
						<td class="columnText">{$match->position}</td>

						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
