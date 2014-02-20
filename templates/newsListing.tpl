{if $objects|count}
<ul class="messageList">
    {foreach from=$objects item=news}
	{assign var="attachments" value=$news->getAttachments()}
        <li>
            <article class="message messageReduced marginTop" data-user-id="{$news->userID}" data-object-id="{$news->newsID}" data-is-deleted="{$news->isDeleted}" data-is-disabled="{$news->isDisabled}">
                <div>
                    <section class="messageContent">
                        <div>
                            <header class="messageHeader">
                                <div class="box32">
                                    {if $news->getImage() != null}
										<a class="framed" href="{link controller='News' object=$news application='cms'}{/link}">
                                        {@$news->getImage()->getImageTag('32')}
										</a>
									{else}
										<a class="framed" href="{link controller='User' object=$news->getUserProfile()}{/link}">
										{@$news->getUserProfile()->getAvatar()->getImageTag(32)}
									</a>
									{/if}
                                    <div class="messageHeadline">
                                        <h1>
                                            <a href="{link controller='News' object=$news application='cms'}{/link}">{$news->getTitle()}</a>
                                        </h1>
										{if $news->languageID != 0 && CMS_NEWS_LANGUAGEICON}
										<p class="newMessageBadge" style="margin-top: 30px">
											{@$news->getLanguageIcon()}
										</p>
										{/if}
										{if $news->isNew()}
										<p class="newMessageBadge">{lang}wcf.message.new{/lang}</p>
										{/if}
                                        <p>
                                            <span class="username">
                                                <a class="userLink" data-user-id="{$news->userID}" href="{link controller='User' object=$news->getUserProfile()}{/link}">
                                                    {$news->username}
                                                </a>
                                            </span>
                                            <a class="permalink" href="{link controller='News' object=$news application='cms'}{/link}">
                                                {@$news->time|time}
                                            </a>
											{if MODULE_LIKE && $__wcf->getSession()->getPermission('user.like.canViewLike') && $news->likes || $news->dislikes}<span class="likesBadge badge jsTooltip {if $news->cumulativeLikes > 0}green{elseif $news->cumulativeLikes < 0}red{/if}" title="{lang likes=$news->likes dislikes=$news->dislikes}wcf.like.tooltip{/lang}">{if $news->cumulativeLikes > 0}+{elseif $news->cumulativeLikes == 0}&plusmn;{/if}{#$news->cumulativeLikes}</span>{/if}
                                        </p>
                                    </div>
                                </div>
                            </header>
                            <div class="messageBody">
                                <div>
                                    {@$news->getExcerpt()}
                                </div>
                                <div class="messageFooter">
                                    <p class="messageFooterNote">
                                        <a href="{link controller='News' object=$news application='cms'}{/link}">
                                            {lang}cms.news.comments.count{/lang}
                                        </a>
                                    </p>
                                </div>
                                <footer class="messageOptions">
                                    <nav class="buttonGroupNavigation jsMobileNavigation">
                                        <ul class="smallButtons buttonGroup">
                                            {event name='messageOptions'}
                                            <li class="continue"><a href="{link controller='News' object=$news application='cms'}{/link}" class="button jsTooltip"><span class="icon icon16 icon-chevron-right"></span> <span>{lang}cms.news.read{/lang}</span></a></li>
                                        </ul>
                                    </nav>
                                </footer>
                            </div>
                        </div>
                    </section>
                </div>
            </article>
        </li>
    {/foreach}
</ul>

{/if}