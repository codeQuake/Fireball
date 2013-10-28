<header id="comments" class="boxHeadline boxSubHeadline">
	<h2>{lang}cms.news.comments{/lang} <span class="badge">{@$commentList->countObjects()}</span></h2>
</header>
<div class="container containerList marginTop">
{include file='__commentJavaScript' commentContainerID='newsCommentList'}
{if $commentList|count ||$commentCanAdd}
<ul id="newsCommentList" class="commentList containerList" data-can-add="true" data-object-id="{@$news->newsID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
        {include file='commentList'}
    </ul>
{else}
    {hascontent}
        <ul id="newsCommentList" class="commentList containerList" data-can-add="false" data-object-id="{@$news->newsID}" data-object-type-id="{@$commentObjectTypeID}" data-comments="{@$commentList->countObjects()}" data-last-comment-time="{@$lastCommentTime}">
            {content}
                {include file='commentList'}
            {/content}
        </ul>
    {hascontentelse}
        <div class="containerPadding">
            {lang}cms.news.comments.noEntries{/lang}
        </div>
    {/hascontent}
{/if}
</div>