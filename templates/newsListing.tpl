{if $objects|count}
<div class="container marginTop shadow" data-type="de.codequake.cms.news">
	<ol class="containerList" data-type="de.codequake.cms.news">
		{foreach from=$objects item=news}
			<li id="news{$news->newsID}" class="jsClipboardObject news {if $news->isDeleted}newsDeleted{/if} {if !$news->isActive}newsDisabled{/if}" {if $news->isDeleted}data-is-deleted="1"{/if} {if !$news->isActive}data-is-active="0"{/if}>
				<input type="checkbox" class="jsClipboardItem" data-object-id="{@$news->newsID}" style="float:left;"/>
        <div class="box128">
          <div style="height: 128px; width: 128px;">
            <a class="framed" href="{link application='cms' controller='News' object=$news}{/link}">{@$news->getImage(128)}</a>
          </div>
          <div class="details">
            <div class="containerHeadline">
              <h3>
                <a data-news-id="{@$news->newsID}" class=" messageGroupLink framed" href="{link application='cms' controller='News' object=$news}{/link}">{$news->subject}</a>
              </h3>
            </div>
            <dl class="plain inlineDataList">
              <dt>{lang}cms.news.author{/lang}</dt>
              <dd>
                {if $news->getUserProfile()->userID != 0}<a class="userLink" data-user-id="{$news->userID}" href="{link controller='User' object=$news->getUserProfile()}{/link}">{$news->username}</a>{else}{$news->username}{/if} ({$news->time|DateDiff})
              </dd>
            </dl>
            <dl class="plain inlineDataList">
              <dt>{lang}cms.news.clicks{/lang}</dt>
              <dd>{$news->clicks}</dd>
            </dl>
            <div class="box24">{@$news->getExcerpt()}</div>
          </div>
        </div>
			</li>
		{/foreach}
	</ol>
</div>
{/if}