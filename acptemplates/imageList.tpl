{include file='header' pageTitle='cms.acp.news.image.list'}

<script data-relocate="true">
	$(function () {
		new WCF.Action.Delete('cms\\data\\news\\image\\NewsImageAction', '.jsImageRow');
	});
</script>

<header class="boxHeadline">
	<h1>{lang}cms.acp.news.image.list{/lang}</h1>
</header>

<div class="contentNavigation">
	{pages print=true assign=pagesLinks application='cms' controller='NewsImageList' link='pageNo=%d&sortField=$sortField&sortOrder=$sortOrder'}

	<nav>
		<ul>
			<li><a href="{link controller='NewsImageAdd' application='cms'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}cms.acp.news.image.add{/lang}</span></a></li>

			{event name='contentNavigationButtonsTop'}
		</ul>
	</nav>
</div>

{if $objects|count}
	<div class="tabularBox tabularBoxTitle marginTop">
		<header>
			<h2>{lang}cms.acp.news.image.list{/lang} <span class="badge badgeInverse">{#$items}</span></h2>
		</header>

		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnImageID" colspan="2">{lang}wcf.global.objectID{/lang}</th>
					<th class="columnTitle">{lang}cms.acp.news.image.title{/lang}</th>
					<th class="columnImage">{lang}cms.acp.news.image{/lang}</th>
					{event name='columnHeads'}
				</tr>
			</thead>

			<tbody>
				{foreach from=$objects item=image}
					<tr class="jsImageRow">
						<td class="columnIcon">
							<span class="icon icon16 fa-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$image->imageID}" data-confirm-message="{lang}cms.acp.news.image.delete.sure{/lang}"></span>
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$image->imageID}</td>
						<td class="columnTitle">{$image->getTitle()}</td>
						<td class="columnImage">{@$image->getImageTag('40')}</td>
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
		
	</div>

	<div class="contentNavigation">
		{@$pagesLinks}

		<nav>
			<ul>
				<li><a href="{link controller='NewsImageAdd' application='cms'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}cms.acp.news.image.add{/lang}</span></a></li>

				{event name='contentNavigationButtonsBottom'}
			</ul>
		</nav>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
