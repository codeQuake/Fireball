{if $objects|count}
<ul class="messageList">
    {foreach from=$objects item=news}
        <li>
            <article class="message messageReduced marginTop" data-user-id="{$news->userID}" data-object-id="{$news->newsID}" data-is-deleted="{$news->isDeleted}" data-is-disabled="{$news->isDisabled}">
                <div>
                    <section class="messageContent">
                        <div>
                            <header class="messageHeader">
                                <div class="box32">
                                    <a class="framed" href="{link controller='User' object=$news->getUserProfile()}{/link}">
                                        {@$news->getUserProfile()->getAvatar()->getImageTag(32)}
                                    </a>
                                    <div class="messageHeadline">
                                        <h1>
                                            <a href="{link controller='News' object=$news application='cms'}{/link}">{$news->getTitle()}</a>
                                        </h1>
                                        <p>
                                            <span class="username">
                                                <a class="userLink" data-user-id="{$news->userID}" href="{link controller='User' object=$news->getUserProfile()}{/link}">
                                                    {$news->username}
                                                </a>
                                            </span>
                                            <a class="permalink" href="{link controller='News' object=$news application='cms'}{/link}">
                                                {@$news->time|time}
                                            </a>
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