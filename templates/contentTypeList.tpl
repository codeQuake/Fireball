<div id="contentTypeList" class="contentTypeList allowScroll">
	<div class="section">
		<a id="contentTypeListClose" class="button buttonPrimary"><span class="icon icon24 fa-times"></span></a>
		<h2 class="sectionTitle">{lang}cms.acp.content.add{/lang}</h2>

		{foreach from=$contentTypes key=category item=types}
			<section class="section" data-category="{$category}">
				<h2 class="sectionTitle">{lang}cms.acp.content.type.{$category}{/lang}</h2>

				<ul id="menu_{$category}" class="sideSubMenu">
					{foreach from=$types item=type}
						<li id="{$type->objectType}" class="draggable">
							<span class="icon icon24 {$type->getProcessor()->getIcon()}"></span>
							{lang}cms.acp.content.type.{$type->objectType}{/lang}
						</li>
					{/foreach}
				</ul>
			</section>
		{/foreach}

		<div class="wideButton formSubmit">
			<button data-type="submit" class="button">
				{lang}wcf.global.button.submit{/lang}
			</button>
		</div>
	</div>
</div>
