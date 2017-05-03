<div id="graph{$content->contentID}" class="pieChartContainer"></div>
{if !$__pieChartJavascriptLoaded|isset}
	<script data-relocate="true" src="//www.google.com/jsapi"></script>
	<script data-relocate="true">google.load("visualization", "1", { packages:["corechart"] })</script>
	{assign var=__pieChartJavascriptLoaded value=true}
{/if}
<script data-relocate="true">
	google.load("visualization", "1", { packages:["corechart"] });
	google.setOnLoadCallback(drawGraph{$content->contentID});
	function drawGraph{$content->contentID}() {
		var data = google.visualization.arrayToDataTable([['title', 'value'], {implode from=$graphOptions item=value key=title}['{$title}',{$value}]{/implode}]);
		var options = {
			title: '{if !$content->contentData[showInlineTitle]|empty}{$content->getTitle()}{/if}',
			backgroundColor: 'transparent',pieHole: 0.4{*
			*}{if $content->contentData[showIn3D]|isset && $content->contentData[showIn3D]},is3D: true{/if}
		};
		var chart = new google.visualization.PieChart(document.getElementById('graph{$content->contentID}'));
		chart.draw(data, options);
	}
	$(function () {
		$('#graph{$content->contentID} svg g').each(function(key, val) {
			{if !$content->contentData[showInlineTitle]|empty}
			if (key == 0) { $(val).find('text').addClass('pieTitle'); } else if (key == 1) { $(val).addClass('pieLegend'); $(val).find('text').addClass('pieLegendItem'); }
			{else}if (key == 0) { $(val).addClass('pieLegend'); $(val).find('text').addClass('pieLegendItem'); }{/if}
		});
	});
</script>