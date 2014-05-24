<div id="newsTableContainer" class="marginTop tabularBox tabularBoxTitle messageGroupList cmsNewsList jsClipboardContainer" data-type="de.codequake.cms.news">
<header>
	<h2>{lang}cms.news.news{/lang}</h2>
</header>
<table class="table">
	<thead>
		<tr>
			{if $__wcf->user->userID && $__wcf->session->getPermission('mod.cms.news.canModerateNews')}<th colspan="2" class="columnMark jsOnly"><label><input type="checkbox" class="jsClipboardMarkAll" /></label></th>{/if}
			<th colspan="2" class="columnTitle columnSubject{if $sortField == 'subject'} active {@$sortOrder}{/if}"><a href="{link application='cms' controller='NewsArchive'}pageNo={@$pageNo}&sortField=subject&sortOrder={if $sortField == 'subject' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.title{/lang}</a></th>
			<th class="columnDigits columnClicks{if $sortField == 'clicks'} active {@$sortOrder}{/if}"><a href="{link application='cms' controller='NewsArchive'}pageNo={@$pageNo}&sortField=clicks&sortOrder={if $sortField == 'clicks' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}cms.news.clicks{/lang}</a></th>
			<th class="columnCategories">{lang}cms.news.category.categories{/lang}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$objects item=news}
		<tr id="news{@$news->newsID}" class="cmsNews jsNewsRow jsClipboardObject {if $news->isNew()}new{/if}" data-news-id="{@$news->newsID}" data-element-id="{@$news->newsID}">
			{if $__wcf->user->userID && $__wcf->session->getPermission('mod.cms.news.canModerateNews')}
			<td class="columnMark jsOnly">
				<label><input type="checkbox" class="jsClipboardItem" data-object-id="{@$news->newsID}" /></label>
			</td>
			<td class="columnMark jsOnly">
				<span class="icon icon16 icon-remove jsTooltip jsDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$news->newsID}" data-confirm-message="{lang}cms.news.delete.sure{/lang}"></span>
			</td>
			{/if}
			<td class="columnIcon columnAvatar">
			{if $news->getUserProfile()->getAvatar()}
				<div>
					<p class="framed">{@$news->getUserProfile()->getAvatar()->getImageTag(32)}</p>
				</div>
			{/if}
			</td>
			<td class="columnText columnSubject">
				<h3>
				 <a class="messageGroupLink cmsNewsLink newsLink" data-news-id="{$news->newsID}" href="{link controller='News' object=$news application='cms'}{/link}">{$news->getTitle()}</a>
				 </h3>
				<aside class="statusDisplay">
					<ul class="statusIcons">
						{if $news->attachments}<li><span class="icon icon16 icon-paper-clip jsTooltip" title="{lang}cms.news.attachments{/lang}"></span></li>{/if}

						{event name='statusIcons'}

						{if CMS_NEWS_LANGUAGEICON && $news->languageID}<li>{@$news->getLanguageIcon()}</li>{/if}
					</ul>
				</aside>

				<small>
					{if $news->userID}<a href="{link controller='User' object=$news->getUserProfile()->getDecoratedObject()}{/link}" class="userLink" data-user-id="{@$news->userID}">{$news->username}</a>{else}{$news->username}{/if}
					- {@$news->time|time}
				</small>

				{event name='newsData'}
			</td>
			<td class="columnDigits columnClicks">
				{#$news->clicks}
			</td>
			<td class="columnCategories">
				{if $news->getCategories()|count}
					<ul class="dataList">
						{foreach from=$news->getCategories() item=category}
							<li><a href="{link application='cms' controller='NewsCategoryList' object=$category}{/link}" class="jsTooltip" title="{lang}cms.news.categorizedNews{/lang}">{$category->getTitle()}</a></li>
						{/foreach}
					</ul>
				{/if}
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
</div>
