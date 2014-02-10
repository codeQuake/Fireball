{include file='header' pageTitle='cms.acp.dashboard'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.dashboard{/lang}</h1>
</header>
<div class="container containerPadding" id="dashboard">
    <div class="center">
        <canvas id="canvas" height="300" width="800"></canvas>
    </div>
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
				    fillColor: "rgba(21,41,148,0.25)",
				    strokeColor: "rgba(21,41,148,0.5)",
				    pointColor: "rgba(21,41,148,1)",
				    pointStrokeColor: "#fff",
				    data: [{foreach from=$visitorArray item=count}{$count['visitors']}, {if $count['visitors'] > $maximum} {assign var=maximum value=$count['visitors']}{/if} {/foreach}]
				},
				{
					fillColor : "rgba(151,187,205,0.5)",
					strokeColor : "rgba(151,187,205,1)",
					pointColor : "rgba(151,187,205,1)",
					pointStrokeColor : "#fff",
					data : [{foreach from=$spiderArray item=count}{$count['visitors']},{/foreach}]
				},
				{
					fillColor : "rgba(148,1,1,0.25)",
					strokeColor : "rgba(148,1,1,0.5)",
					pointColor : "rgba(148,1,1,1)",
					pointStrokeColor : "#fff",
					data : [{foreach from=$userArray item=count}{$count['visitors']},{/foreach}]
				}
            ]

            }
			
			
        var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Line(lineChartData, {
				scaleOverride : true,
				scaleSteps : {($maximum+5)/5},
				scaleStepWidth: 5
			});

	</script>
</div>

{include file='footer'}