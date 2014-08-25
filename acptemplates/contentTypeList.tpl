{foreach from=$contentTypes key=category item=types}
	<fieldset>
		<legend>{lang}cms.acp.content.type.{$category}{/lang}</legend>

		<ul class="containerBoxList tripleColumned">
			{foreach from=$types item=type}
				<li>
					<a href="{link controller='ContentAdd' application='cms' id=$pageID}objectType={$type->objectType}&position={$position}&parentID={$parentID}{/link}"><span class="icon icon16 {$type->getProcessor()->getIcon()}"></span> {lang}cms.acp.content.type.{$type->objectType}{/lang}</a>
				</li>
			{/foreach}
		</ul>
	</fieldset>
{/foreach}
