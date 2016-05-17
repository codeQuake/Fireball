{if $user != null}
	<div class="userCard box128">
		<a class="framed" href="{link controller='User' object=$user}{/link}">{@$user->getAvatar()->getImageTag(128)}</a>
		<div class="boxHeadline">
			<nav class="jsMobileNavigation buttonGroupNavigation">
				<ul class="buttonList iconList">
					{if $user->homepage != ""}
						<li>
							<a href="{$user->homepage}" title="{lang}wcf.user.option.homepage{/lang}" class="jsTooltip">
								<span class="icon icon16 icon-home"></span>
								<span class="invisible">{lang}wcf.user.option.homepage{/lang}</span>
							</a>
						</li>
					{/if}
					{if $user->facebook != ""}
						<li>
							<a href="http://facebook.com/{$user->facebook}" title="{lang}wcf.user.option.facebook{/lang}" class="jsTooltip">
								<span class="icon icon16 icon-facebook"></span>
								<span class="invisible">{lang}wcf.user.option.facebook{/lang}</span>
							</a>
						</li>
					{/if}
					{if $user->twitter != ""}
						<li>
							<a href="http://twitter.com/{$user->twitter}" title="{lang}wcf.user.option.twitter{/lang}" class="jsTooltip">
								<span class="icon icon16 icon-twitter"></span>
								<span class="invisible">{lang}wcf.user.option.twitter{/lang}</span>
							</a>
						</li>
					{/if}
				</ul>
			</nav>
			<h2><a class="framed userLink" href="{link controller='User' object=$user}{/link}" data-user-id="{$user->userID}">{@$user->username}</a></h2>
			<p class="title">{$user->getUserTitle()}</p>
		</div>
		
		<div class="details marginTop">
			{@$user->getFormattedUserOption('aboutMe')}
		</div>
	</div>
{/if}
