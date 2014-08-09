<header id="comments" class="boxHeadline boxSubHeadline">
	<h2>{lang}cms.page.comments{/lang} <span class="badge">{@$commentList->countObjects()}</span></h2>
</header>

{include file='__commentJavaScript' commentContainerID='pageCommentList'}

<div class="container containerList marginTop">
	{if $commentCanAdd}
		<ul id="pageCommentList" class="commentList containerList" data-can-add="true" data-object-id="{@$page->pageID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
			{include file='commentList'}
		</ul>
	{else}
		{hascontent}
			<ul id="pageCommentList" class="commentList containerList" data-can-add="false" data-object-id="{@$page->pageID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
				{content}
					{include file='commentList'}
				{/content}
			</ul>
		{hascontentelse}
			<div class="containerPadding">
				{lang}cms.page.comments.noComments{/lang}
			</div>
		{/hascontent}
	{/if}
</div>
