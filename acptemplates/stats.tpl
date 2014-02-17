{include file='header' pageTitle='cms.acp.stats'}
<header class="boxHeadline">
    <h1>{lang}cms.acp.stats{/lang}</h1>
</header>
		<div  class="container containerPadding shadow marginTop">
		<fieldset>
			<legend>{lang}cms.acp.stats.vistors{/lang}</legend>
			<div class="center">		
				<canvas id="canvas" height="200" width="800"></canvas>
			</div>
		</fieldset>
		</div>
        <div class="container containerPadding shadow marginTop" style="width: 30%; float: left; box-sizing:border-box; margin-right: 2%;">
            <fieldset>
			<legend>{lang}cms.acp.stats.browsers{/lang}</legend>
			<div class="center">		
				<canvas id="browsers" height="200" width="200"></canvas>                
			</div>
                
                    {assign var=i value=0}
                    <dl class="plain inlineDataList">
                    {foreach from=$browsers item=browser}
                        <dt style="float:left;"><span class="icon icon-circle" style="color: {$colors[$i]};"></span> <small>{$browser['browser']}</small></dt>
                        <dd style="display: block; text-align: right;"><small>{$browser['percentage']} %</small></dd>
                    {assign var=i value=$i+1}
                    {/foreach}
                    </dl>
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
                         data: [{foreach from=$visits item=count}{$count['visitors']}, {if $count['visitors'] > $maximum} {assign var=maximum value=$count['visitors']}{/if} {/foreach}]
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
            value: {$browser['amount']},
            color: "{$colors[$i]}"
        },
        {assign var=i value=$i+1}
                    {/foreach}
                    ]
        var myDonut = new Chart(document.getElementById("browsers").getContext("2d")).Doughnut(data);
    </script>
{include file='footer'}