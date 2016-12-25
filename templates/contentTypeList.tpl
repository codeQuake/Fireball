<div id="contentTypeList" class="contentTypeList leftSide allowScroll">
	<div class="container containerPadding marginTop">
		<a id="contentTypeListMove" class="button buttonPrimary"><span class="icon icon24 icon-angle-right"></span></a>
		<h2 class="contentTitle">{lang}cms.acp.content.add{/lang}</h2>

		{foreach from=$contentTypes key=category item=types}
			<div class="container containerPadding marginTop" data-category="{$category}">
				<h2 class="contentTitle">{lang}cms.acp.content.type.{$category}{/lang}</h2>

				<ul id="menu_{$category}" class="sideSubMenu">
					{foreach from=$types item=type}
						<li id="{$type->objectType}" class="draggable">
							<span class="icon icon24 {$type->getProcessor()->getIcon()}"></span>
							{lang}cms.acp.content.type.{$type->objectType}{/lang}
						</li>
					{/foreach}
				</ul>
			</div>
		{/foreach}

		<div class="wideButton formSubmit">
			<button data-type="submit" class="button">
				{lang}wcf.global.button.submit{/lang}
			</button>
		</div>
	</div>
</div>

<script data-relocate="true">
	$(function() {
		$('#contentTypeListMove').click(function () {
			if ($('#contentTypeList').hasClass('leftSide')) {
				$('#contentTypeListMove > span').removeClass('icon-angle-right').addClass('icon-angle-left');
				$('#contentTypeList').removeClass('leftSide').addClass('rightSide');
			} else {
				$('#contentTypeListMove > span').removeClass('icon-angle-left').addClass('icon-angle-right');
				$('#contentTypeList').removeClass('rightSide').addClass('leftSide');
			}
		})
	});
</script>
