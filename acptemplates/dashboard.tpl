{include file='header' pageTitle='cms.acp.dashboard'}

<header class="boxHeadline">
	<h1>{lang}cms.acp.dashboard{/lang}</h1>
</header>

		{assign var=visitorArray value=$visitors->getWeeklyVisitorArray()}
		<div  class="container containerPadding shadow marginTop">
		<fieldset>
			<legend>{lang}cms.acp.dashboard.lastWeeksVisitors{/lang}</legend>
			<div class="center">
				<div id="canvas" height="250"></div>
			</div>

		</fieldset>
		</div>
		<div style="float: left; width: 49%; box-sizing: border-box; margin-right: 1%;">
			<div class="container containerPadding shadow marginTop">
				<fieldset>
					<legend>{lang}cms.acp.dashboard.lastNews{/lang}</legend>
					<ul>

						{foreach from=$feed item=item}
						{if $item['title']|isset}
						<li style="border-bottom: 1px dashed #dfdfdf; padding: 5px; margin-bottom: 5px;">
							<div class="containerHeadline">
								<h3><a href="{$item['link']}">{$item['title']}</a></h3>
								<small>{$item['date']}</small>
							</div>
							<div>
								{@$item['description']}
							</div>
						</li>
						{else}
							<strong>Fatal Error:</strong> {$item}
						{/if}
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
						<dt>{lang}cms.page.news{/lang}
						<dd>{#$news|count}</dd>
						</dl>
					<dl class="plain statsDataList">
						<dt>{lang}wcf.user.usersOnline{/lang}</dt>
						<dd>{#$usersOnlineList->stats[total]}</dd>
						<dt>{lang}cms.acp.dashboard.visitsToday{/lang}</dt>
						<dd>{$visitorArray[6][visitors]['visits']}</dd>
						<dt>{lang}cms.acp.dashboard.visitsYesterday{/lang}</dt>
						<dd>{$visitorArray[5][visitors]['visits']}</dd>
						<dt>{lang}cms.acp.dashboard.visitsAll{/lang}</dt>
						<dd>{$visitors->getAllVisitors()}</dd>
					</dl>
				</fieldset>
			</div>
		</div>
		<br class="clearfix" />
	{assign var=maximum value=0}
	<script data-relocate="true" src="https://www.google.com/jsapi"></script>
	<script data-relocate="true">
		{literal}google.load("visualization", "1", {packages:["corechart"]});{/literal}
		google.setOnLoadCallback(drawArea);
		function drawArea() {
				var data = google.visualization.arrayToDataTable([
				['Visits', '{lang}cms.acp.dashboard.all{/lang}', '{lang}cms.acp.dashboard.registered{/lang}', '{lang}cms.acp.dashboard.spiders{/lang}'],
				{foreach from=$visitorArray item=visit}
				['{$visit['string']}', {if $visit['visitors']['visits']|isset}{$visit['visitors']['visits']}{else}0{/if}, {if $visit['visitors']['users']|isset}{$visit['visitors']['users']}{else}0{/if}, {if $visit['visitors']['spiders']|isset}{$visit['visitors']['spiders']}{else}0{/if}],
				{/foreach}
			]);

			var options = {
				title: '',
				backgroundColor: 'transparent',
				fontName: 'Trebuchet MS',
				{literal}
				legend: {position: 'bottom'},
				vAxis: {minValue: 0}
				{/literal}
			};

			var chart = new google.visualization.AreaChart(document.getElementById('canvas'));
			chart.draw(data, options);
		}
	</script>


{include file='footer'}
