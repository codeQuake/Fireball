{foreach from=$contentTypes key=category item=types}
<section class="section">
		<h2 class="sectionTitle">{lang}fireball.acp.content.type.{$category}{/lang}</h2>

		<ul class="containerBoxList tripleColumned">
			{foreach from=$types item=type}
				<li>
					<a href="{link controller='ContentAdd' application='cms'}pageID={$page->pageID}&objectType={$type->objectType}&position={$position}&parentID={$parentID}{/link}"><span class="icon icon16 {$type->getProcessor()->getIcon()}"></span> {lang}fireball.acp.content.type.{$type->objectType}{/lang}</a>
				</li>
			{/foreach}
		</ul>
	</section>
{/foreach}
