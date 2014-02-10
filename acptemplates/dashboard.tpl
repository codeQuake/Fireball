{include file='header' pageTitle='cms.acp.dashboard'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.dashboard{/lang}</h1>
</header>
<div class="container containerPadding" id="dashboard">
    <div class="center">
        <canvas id="canvas" height="300" width="800"></canvas>
    </div>
	{assign var=visitorArray value=$visitors->getWeeklyVisitorArray()}
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
				    data: [{foreach from=$visitorArray item=count}{$count['visitors']},{/foreach}]
				}
            ]

            }
			

        var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Line(lineChartData, {
				scaleOverride : true,
				scaleSteps : 10,
				scaleStepWidth : 1,
				scaleStartValue : 0,
			});

	</script>
</div>

{include file='footer'}