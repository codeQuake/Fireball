{include file='googleMapsJavaScript'}
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		var $map = new WCF.Location.GoogleMaps.Map('gmap{$contentID}');
		WCF.Location.GoogleMaps.Util.focusMarker($map.addMarker({@$latitude}, {@$longitude}, '{$title|encodeJS}'));
	});
	//]]>
</script>

<div class="googleMap" id="gmap{$contentID}"></div>
