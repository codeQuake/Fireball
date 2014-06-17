<div class="tabularBox tabularBoxTitle">
	<table class="table">
		<thead>
			<tr>
				<th class="columnID" colspan="2">{lang}wcf.global.objectID{/lang}</th>
				<th class="columnTitle">{lang}cms.acp.page.revision{/lang}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$versions item=version}
			<tr class="jsVersionRow">
				<td class="columnIcon">
					<span class="icon icon16 icon-cogs jsRestoreRevisionButton jsTooltip pointer" title="{lang}cms.acp.page.revision.restore{/lang}" data-object-id="{@$version->versionID}" data-confirm-message="{lang}cms.acp.page.revision.restore.sure{/lang}"></span>
					{event name='rowButtons'}
				</td>
				<td class="columnID">{@$version->versionID}</td>
				<td class="columnTitle">{lang}cms.acp.page.revision.title{/lang}</td>

				{event name='columns'}
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>
