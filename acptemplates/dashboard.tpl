{include file='header' pageTitle='cms.acp.dashboard'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.dashboard{/lang}</h1>
</header>
		
		<div  class="container containerPadding shadow marginTop">
		<fieldset>
			<legend>{lang}cms.acp.dashboard.lastWeeksVisitors{/lang}</legend>
			<div class="center">		
				<canvas id="canvas" height="200" width="800"></canvas>
				<div class="legend">
					<span class="icon icon16 icon-circle" style="color:rgba(21,41,148,1);"></span> {lang}cms.acp.dashboard.all{/lang}
					<span class="icon icon16 icon-circle" style="color:rgba(148,1,1,1);" ></span> {lang}cms.acp.dashboard.registered{/lang}
					<span class="icon icon16 icon-circle" style="color:rgba(151,187,205,1);" ></span> {lang}cms.acp.dashboard.spiders{/lang}
				</div
			</div>
		
		</fieldset>
		</div>
		<div style="float: left; width: 49%; box-sizing: border-box; margin-right: 1%;">
			<div class="container containerPadding shadow marginTop">
				<fieldset>
					<legend>{lang}cms.acp.dashboard.lastNews{/lang}</legend>
					<ul>
						{foreach from=$feed item=item} 
						<li style="border-bottom: 1px dashed #dfdfdf; padding: 5px; margin-bottom: 5px;">
							<div class="containerHeadline">
								<h3><a href="{$item['link']}">{$item['title']}</a></h3>
								<small>{$item['date']}</small>
							</div>
							<div>
								{@$item['description']}
							</div>
						</li>
						{/foreach}
					</ul>
				</fieldset>
			</div>
		</div>
		<div style="float: left; width: 49%; box-sizing: border-box; margin-left: 1%;">
			<div class="container containerPadding shadow marginTop">
				<fieldset>
					<legend>{lang}Informationen{/lang}</legend>
					<dl class="plain statsDataList">
						<dt>{lang}wcf.acp.index.system.software{/lang}: <a href="http://codequake.de">Fireball CMS</a> </dt>
						<dd>{$__cms->getPackage()->packageVersion}</dd>
						<dt>{lang}wcf.acp.index.system.php{/lang}</dt>
						<dd><a href="{link controller='PHPInfo'}{/link}">{PHP_VERSION}</a></dd>
						<dt>{lang}wcf.acp.index.system.mySQLVersion{/lang}</dt>
						<dd>{$server[mySQLVersion]}</dd>
					</dl>
					<dl class="plain statsDataList">
						<dt>{lang}cms.acp.page.list{/lang}</dt>
						<dd>{#$pages|count}</dd>
						<dt>{lang}cms.acp.menu.link.cms.news{/lang}
						<dd>{#$news|count}</dd>
						</dl>
					<dl class="plain statsDataList">
						<dt>{lang}wcf.user.usersOnline{/lang}</dt>
						<dd>{#$usersOnlineList->stats[total]}</dd>
						<dt>{lang}cms.acp.dashboard.visitsToday{/lang}</dt>
						<dd>{$visitors->getWeeklyVisitorArray()[6][visitors]}</dd>
						<dt>{lang}cms.acp.dashboard.visitsYesterday{/lang}</dt>
						<dd>{$visitors->getWeeklyVisitorArray()[5][visitors]}</dd>						
					</dl>
				</fieldset>
			</div>
		</div>
		<br class="clearfix" />
	{assign var=visitorArray value=$visitors->getWeeklyVisitorArray()}
	{assign var=userArray value=$visitors->getWeeklyVisitorArray("registered")}
	{assign var=spiderArray value=$visitors->getWeeklyVisitorArray("spiders")}
	{assign var=maximum value=0}
    <script data-relocate="true" src="{@$__wcf->getPath('cms')}js/3rdParty/Chart.js"></script>
    <script data-relocate="true">
        var lineChartData = {
            labels: [{foreach from=$visitorArray item=month}"{$month['string']}",{/foreach}],
            datasets: [
				{
				    fillColor: "rgba(21,41,148,0.1)",
				    strokeColor: "rgba(21,41,148,0.5)",
				    pointColor: "rgba(21,41,148,1)",
				    pointStrokeColor: "#fff",
				    data: [{foreach from=$visitorArray item=count}{$count['visitors']}, {if $count['visitors'] > $maximum} {assign var=maximum value=$count['visitors']}{/if} {/foreach}]
				},
				{
					fillColor : "rgba(151,187,205,0.1)",
					strokeColor : "rgba(151,187,205,1)",
					pointColor : "rgba(151,187,205,1)",
					pointStrokeColor : "#fff",
					data : [{foreach from=$spiderArray item=count}{$count['visitors']},{/foreach}]
				},
				{
					fillColor : "rgba(148,1,1,0.1)",
					strokeColor : "rgba(148,1,1,0.5)",
					pointColor : "rgba(148,1,1,1)",
					pointStrokeColor : "#fff",
					data : [{foreach from=$userArray item=count}{$count['visitors']},{/foreach}]
				}
            ]

            }
			
			
        var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Line(lineChartData, {
				scaleOverride : true,
				scaleSteps : {if $maximum <= 50}{($maximum+5)/5}{elseif $maximum <=100}{($maximum+10)/10}{elseif $maximum <= 300}{($maximum+20)/20}{elseif $maximum <= 500}{($maximum+50)/50}{elseif $maximum <= 700}{($maximum+100)/100}{else}{($maximum+200)/200}{/if},
				scaleStepWidth: {if $maximum <= 50}5{elseif $maximum <=100}10{elseif $maximum <= 300}20{elseif $maximum <= 500}50{elseif $maximum <= 700}100{else}200{/if}
			});

	</script>
</div>

{include file='footer'}