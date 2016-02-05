<header class="boxHeadline boxSubHeadline">
	<h2>{lang}wcf.dashboard.box.de.codequake.cms.visitorsWeekly{/lang}</h2>
</header>

<div class="container marginTop">
	<div class="center">
		<div id="visitorsWeeklyCanvas" style="height: 250px;"></div>
	</div>
</div>

{* import google's js api *}
<script data-relocate="true" src="https://www.google.com/jsapi"></script>

<script data-relocate="true">
	google.load("visualization", "1", { packages:["corechart"] });

	google.setOnLoadCallback(drawArea);

	function drawArea() {
		var data = google.visualization.arrayToDataTable([
			['Visits', '{lang}fireball.dashboard.all{/lang}', '{lang}fireball.dashboard.registered{/lang}', '{lang}fireball.dashboard.spiders{/lang}'],
			{foreach from=$visits item=visit}
				['{$visit['string']}', {if $visit['visitors']['visits']|isset}{$visit['visitors']['visits']}{else}0{/if}, {if $visit['visitors']['users']|isset}{$visit['visitors']['users']}{else}0{/if}, {if $visit['visitors']['spiders']|isset}{$visit['visitors']['spiders']}{else}0{/if}],
			{/foreach}
		]);

		var options = {
			title: '',
			backgroundColor: 'transparent',
			fontName: 'Trebuchet MS',
			legend: { position: 'bottom' },
			vAxis: { minValue: 0 }
		};

		var chart = new google.visualization.AreaChart(document.getElementById('visitorsWeeklyCanvas'));
		chart.draw(data, options);
	}
</script>

