<div class="sideMenu">
	<div class="sideDescription containerPadding">
		<header class="containerHeadline">
			<h3>Blabla</h3>
		</header>
		<small>
			Blablabla Drag und Drop und so, hier kommt noch eine Sprachvariable hin zum erklären, dass Bäume Blätter haben und so...
		</small>
	</div>
	<ul>
		{foreach from=$contentTypes key=category item=types}
		<li data-category="{$category}">
			<p>{lang}fireball.acp.content.type.{$category}{/lang}</p>
			<ul id="menu_{$category}" class="sideSubMenu">
				{foreach from=$types item=type}
				<li id="{$type->objectType}" class="draggable">
					<p>
						<span class="icon icon24 {$type->getProcessor()->getIcon()}"></span>
						{lang}fireball.acp.content.type.{$type->objectType}{/lang}
					</p>
				</li>
				{/foreach}
			</ul>
		</li>
		{/foreach}
	</ul>
</div>
<div class="wideButton formSubmit">
	<button data-type="submit" class="button">
		{lang}wcf.global.button.submit{/lang}
	</button>
</div>
