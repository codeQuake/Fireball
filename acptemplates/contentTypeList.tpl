<fieldset>
	<legend>{lang}cms.acp.content.type.content{/lang}</legend>
	<ul class="tripleColumned">
		{foreach from=$contentTypes item=type}
			<li><a href="{link controller='ContentAdd' application='cms' id=$pageID}objectType={$type->objectType}{/link}"><span class="icon icon16 {$type->getProcessor()->getIcon()}"></span> {lang}cms.acp.content.type.{$type->objectType}{/lang}</li>
		{/foreach}
	</ul>
</fieldset>