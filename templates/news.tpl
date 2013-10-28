{include file='documentHeader'}
<head>
	<title>{$news->getTitle()|language} - {PAGE_TITLE|language}</title>
	
	<link rel="canonical" href="{link application='cms' controller='News' object=$news}{/link}" />
	{include file='headInclude' application='wcf'}
    
	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Moderation{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
	<script data-relocate="true" src="{@$__wcf->getPath('cms')}js/CMS.js?v={@$__wcfVersion}"></script>
    <script data-relocate="true">
        //<![CDATA[
        $(function () {
            new WCF.Action.Delete('cms\\data\\news\\NewsAction', '.jsNews');
			new WCF.Message.Share.Content();
			{if LOG_IP_ADDRESS && $__wcf->session->getPermission('admin.user.canViewIpAddress')}new CMS.News.IPAddressHandler();{/if}
        });
		//]]>
	</script>
</head>

<body id="tpl{$templateName|ucfirst}">

{capture assign='sidebar'}

    {event name='boxes'}
    {@$__boxSidebar}
{/capture}
{if !$anchor|isset}{assign var=anchor value=$__wcf->getAnchor('top')}{/if}

{include file='header' sidebarOrientation='right'}

<header class="boxHeadline">
		<h1>{$news->getTitle()|language}</h1>
</header>

{include file='userNotice'}

<ul class="messageList">
    <li>
            <article class="message messageReduced marginTop jsNews" data-user-id="{$news->userID}" data-object-id="{$news->newsID}" data-is-deleted="{$news->isDeleted}" data-is-disabled="{$news->isDisabled}">
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
                                    {@$news->getFormattedMessage()}
                                </div>
                               
                                <footer class="messageOptions">
                                    <nav class="buttonGroupNavigation jsMobileNavigation">
                                        <ul class="smallButtons buttonGroup">
											{if $news->canModerate()}<li><a href="{link controller='NewsEdit' application='cms' object=$news}{/link}" class="button jsMessageEditButton" title="{lang}wcf.global.button.edit{/lang}"><span class="icon icon16 icon-pencil"></span> <span>{lang}wcf.global.button.edit{/lang}</span></a></li>{/if}
											{if LOG_IP_ADDRESS && $news->ipAddress && $__wcf->session->getPermission('admin.user.canViewIpAddress')}<li class="jsIpAddress jsOnly" data-news-id="{@$news->newsID}"><a title="{lang}cms.news.ipAddress{/lang}" class="button jsTooltip"><span class="icon icon16 icon-globe"></span> <span class="invisible">{lang}cms.news.ipAddress{/lang}</span></a></li>{/if}											{if $news->canModerate()}<li class="jsOnly"><div class="button"><span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$news->newsID}" data-confirm-message="{lang}cms.news.delete.sure{/lang}"></span></div></li>{/if}
											{event name='messageOptions'}
											<li class="toTopLink"><a href="{@$anchor}" title="{lang}wcf.global.scrollUp{/lang}" class="button jsTooltip"><span class="icon icon16 icon-arrow-up"></span> <span class="invisible">{lang}wcf.global.scrollUp{/lang}</span></a></li>
                                            
                                        </ul>
                                    </nav>
                                </footer>
                            </div>
                        </div>
                    </section>
                </div>
            </article>
        </li>
    </ul>
    <div class="contentNavigation">
        <nav>
            <ul>
                <li><a href="{link application='cms' controller='News' object=$news appendSession=false}{/link}" class="button jsButtonShare jsOnly" data-link-title="{$news->subject}"><span class="icon icon16 icon-link"></span> <span>{lang}wcf.message.share{/lang}</span></a></li>
			{event name='contentNavigationButtonsBottom'}
            </ul>
        </nav>
        {if ENABLE_SHARE_BUTTONS}
		{include file='shareButtons'}
	    {/if}
    </div>
    {include file='newsCommentList' application='cms'}
{include file='footer' sandbox=false}
</body>
</html>