{include file='header' pageTitle='cms.acp.stats'}
<header class="boxHeadline">
    <h1>{lang}cms.acp.stats{/lang}</h1>
</header>

<div class="contentNavigation">
    <form method="post" action="{link application='cms' controller='Stats'}{/link}">
        <input type="date" name="startDate" value="{$startDate|date}"/>
        <input type="date" name="endDate" value="{$endDate|date}"/>
        <input type="submit" />
    </form>
</div>
		<div  class="container containerPadding shadow marginTop">
		<fieldset>
			<legend>{lang}cms.acp.stats.vistors{/lang}</legend>
			<div class="center">		
				<canvas id="canvas" height="200" width="800"></canvas>
			</div>
		</fieldset>
		</div>
        <div class="container containerPadding shadow marginTop" style="float: left; width: 49%; box-sizing: border-box; margin-right: 1%;">
            <fieldset>
			<legend>{lang}cms.acp.stats.browsers{/lang}</legend>
			<div class="center">		
				<canvas id="browsers" height="200" width="200"></canvas>                
			</div>
                
                    {assign var=i value=0}
                    <dl class="plain inlineDataList">
                    {foreach from=$browsers item=values key=$key}
                        <dt style="float:left;"><span class="icon icon-circle" style="color: {$colors[$i]};"></span> <small>{$key}</small></dt>
                        <dd style="display: block; text-align: right;"><small>{$values['percentage']} %</small></dd><p style="clear:both;" ></p>
                    {assign var=i value=$i+1}
                    {/foreach}
                    </dl>
		</fieldset>
        </div>
        <div class="container containerPadding shadow marginTop clearfix" style="float: left; width: 49%; box-sizing: border-box; margin-left: 1%;">
            <fieldset>
			<legend>{lang}cms.acp.stats.mostClicked{/lang}</legend>
			<table class="table">
                <thead>
                    <tr>
                        <th>{lang}cms.acp.stats.page{/lang}</th>
                        <th>{lang}cms.acp.stats.clicks{/lang}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$pages item=page}
                        <tr>
                            <td>{$page->getTitle()|language}</td>
                            <td>{$page->clicks}</td>
                        </tr>
                    {/foreach}
                </tbody>
			</table>
		    </fieldset>
        </div> 
        <br style="clear: both;" />
        <div  class="container containerPadding shadow marginTop">
            <fieldset>
			<legend>{lang}cms.acp.stats.vistors{/lang}</legend>
                {assign var=usersOnlineList value=''}
{assign var=usersOnline value=0}
{assign var=robotsOnlineList value=''}
{assign var=robotsOnline value=0}
{assign var=guestsOnlineList value=''}
{assign var=guestsOnline value=0}
{foreach from=$objects item=user}
	{capture assign=locationData}
		<p>
			{if $user->getLocation()}{@$user->getLocation()}{else}{lang}wcf.user.usersOnline.location.unknown{/lang}{/if} <small>- {@$user->lastActivityTime|time}</small>
		</p>
	{/capture}
	
	{capture assign=sessionData}
		{if $__wcf->session->getPermission('admin.user.canViewIpAddress')}
			<dl class="plain inlineDataList">
				<dt>{lang}wcf.user.usersOnline.ipAddress{/lang}</dt>
				<dd title="{$user->getFormattedIPAddress()}">{$user->getFormattedIPAddress()|truncate:30}</dd>
				
				{if !$user->spiderID}
					<dt>{lang}wcf.user.usersOnline.userAgent{/lang}</dt>
					<dd title="{$user->userAgent}">{$user->getBrowser()|truncate:30}</dd>
				{/if}
			</dl>
		{/if}
	{/capture}
	
	{if $user->userID}
		{* member *}
		{capture append=usersOnlineList}
			<li>
				<div class="box48">
					<a href="{link controller='User' object=$user}{/link}" title="{$user->username}" class="framed">{@$user->getAvatar()->getImageTag(48)}</a>
					
					<div class="details userInformation">
						<div class="containerHeadline">
							<h3><a href="{link controller='User' object=$user}{/link}">{@$user->getFormattedUsername()}</a>{if MODULE_USER_RANK && $user->getUserTitle()} <span class="badge userTitleBadge{if $user->getRank() && $user->getRank()->cssClassName} {@$user->getRank()->cssClassName}{/if}">{$user->getUserTitle()}</span>{/if}</h3>
							{@$locationData} 
						</div>
						
						{@$sessionData}
						
					</div>
				</div>
			</li>
		{/capture}
		
		{assign var=usersOnline value=$usersOnline+1}
	{elseif $user->spiderID}
		{* search robot *}
		{capture append=robotsOnlineList}
			<li>
				<div class="box48">
					<p class="framed"><img src="{$__wcf->getPath()}images/avatars/avatar-spider-default.svg" alt="" class="icon48" /></p>
					
					<div class="details userInformation">
						<div class="containerHeadline">
							<h3>{if $user->getSpider()->spiderURL}<a href="{$user->getSpider()->spiderURL}" class="externalURL"{if EXTERNAL_LINK_TARGET_BLANK} target="_blank"{/if}>{$user->getSpider()->spiderName}</a>{else}{$user->getSpider()->spiderName}{/if}</h3>
							{@$locationData} 
						</div>
						
						{@$sessionData}
					</div>
				</div>
			</li>
		{/capture}
		
		{assign var=robotsOnline value=$robotsOnline+1}
	{else}
		{* unregistered *}
		{capture append=guestsOnlineList}
			<li>
				<div class="box48">
					<p class="framed"><img src="{$__wcf->getPath()}images/avatars/avatar-default.svg" alt="" class="icon48" /></p>
					
					<div class="details userInformation">
						<div class="containerHeadline">
							<h3>{lang}wcf.user.guest{/lang}</h3>
							{@$locationData} 
						</div>
						
						{@$sessionData}
					</div>
				</div>
			</li>
		{/capture}
		
		{assign var=guestsOnline value=$guestsOnline+1}
	    {/if}
    {/foreach}
    {if $usersOnline}
	    <header class="boxHeadline">
		    <h1>{lang}wcf.user.usersOnline{/lang} <span class="badge">{#$usersOnline}</span></h1>
	    </header>
	
	    <div class="container marginTop">
		    <ol class="containerList doubleColumned userList">
			    {@$usersOnlineList}
		    </ol>
	    </div>
    {/if}

    {if $guestsOnline && USERS_ONLINE_SHOW_GUESTS}
	    <header class="boxHeadline">
		    <h1>{lang}wcf.user.usersOnline.guests{/lang} <span class="badge">{#$guestsOnline}</span></h1>
	    </header>
	
	    <div class="container marginTop">
		    <ol class="containerList doubleColumned">
			    {@$guestsOnlineList}
		    </ol>
	    </div>
    {/if}

    {if $robotsOnline && USERS_ONLINE_SHOW_ROBOTS}
	    <header class="boxHeadline">
		    <h1>{lang}wcf.user.usersOnline.robots{/lang} <span class="badge">{#$robotsOnline}</span></h1>
	    </header>
	
	    <div class="container marginTop">
		    <ol class="containerList doubleColumned">
			    {@$robotsOnlineList}
		    </ol>
	    </div>
    {/if}

            </fieldset>
        </div>
	{assign var=maximum value=0}
    <script data-relocate="true" src="{@$__wcf->getPath('cms')}js/3rdParty/Chart.js"></script>
    <script data-relocate="true">
     var lineChartData = {
         labels: [{foreach from=$visits item=month}"{$month['string']}",{/foreach}],
                 datasets: [
				{
				    fillColor: "rgba(21,41,148,0.1)",
				    strokeColor: "rgba(21,41,148,0.5)",
				    pointColor: "rgba(21,41,148,1)",
				    pointStrokeColor: "#fff",
				    data: [{foreach from=$visits item=count}{if $count['visitors']['visits']|isset}{$count['visitors']['visits']}, {if $count['visitors']['visits'] > $maximum} {assign var=maximum value=$count['visitors']['visits']}{/if}{else}0,{/if} {/foreach}]
				},
				{
					fillColor : "rgba(151,187,205,0.1)",
					strokeColor : "rgba(151,187,205,1)",
					pointColor : "rgba(151,187,205,1)",
					pointStrokeColor : "#fff",
					data : [{foreach from=$visits item=count}{if $count['visitors']['spiders']|isset} {$count['visitors']['spiders']}{else}0{/if},{/foreach}]
				},
				{
					fillColor : "rgba(148,1,1,0.1)",
					strokeColor : "rgba(148,1,1,0.5)",
					pointColor : "rgba(148,1,1,1)",
					pointStrokeColor : "#fff",
					data : [{foreach from=$visits item=count}{if $count['visitors']['users']|isset}{$count['visitors']['users']}{else}0{/if},{/foreach}]
				}
            ]
                         }
			
			
        var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Line(lineChartData, {
            scaleOverride : true,
            scaleSteps : {if $maximum <= 50}{($maximum+5)/5}{elseif $maximum <=100}{($maximum+10)/10}{elseif $maximum <= 300}{($maximum+20)/20}{elseif $maximum <= 500}{($maximum+50)/50}{elseif $maximum <= 700}{($maximum+100)/100}{else}{($maximum+200)/200}{/if},
         scaleStepWidth: {if $maximum <= 50}5{elseif $maximum <=100}10{elseif $maximum <= 300}20{elseif $maximum <= 500}50{elseif $maximum <= 700}100{else}200{/if}
         });

	</script>
    {assign var=i value=0}
    <script data-relocate="true">
                var data = [
                    {foreach from=$browsers item=browser}
        {
            value: {$browser['visits']},
            color: "{$colors[$i]}"
        },
        {assign var=i value=$i+1}
                    {/foreach}
                    ]
        var myDonut = new Chart(document.getElementById("browsers").getContext("2d")).Doughnut(data);
    </script>
{include file='footer'}