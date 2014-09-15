{if $revisions|count}
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new CMS.ACP.Content.Revisions.Restore();
		});
		//]]>
	</script>

	<div class="marginTop tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th class="columnID" colspan="2">{lang}wcf.global.objectID{/lang}</th>
					<th class="columnAction">{lang}cms.acp.content.revision.action{/lang}</th>
					<th class="columnUser">{lang}wcf.user.username{/lang}</th>
					<th class="columnTime">{lang}cms.acp.content.revision.time{/lang}</th>

					{event name='columnHeads'}
				</tr>
			</thead>
			<tbody>
				{foreach from=$revisions item=revision}
					<tr class="jsRevisionRow">
						<td class="columnIcon">
							<span class="icon icon16 icon-undo jsRestoreRevisionButton jsTooltip pointer" title="{lang}cms.acp.content.revision.restore{/lang}" data-object-id="{@$revision->revisionID}" data-content-id="{@$revision->contentID}" data-confirm-message="{lang}cms.acp.content.revision.restore.sure{/lang}"></span>
							{event name='rowButtons'}
						</td>
						<td class="columnID">{@$revision->revisionID}</td>
						<td class="columnAction">{lang}cms.acp.content.revision.action.{$revision->action}{/lang}</td>
						<td class="columnUser">{@$revision->username}</td>
						<td class="columnTime">{@$revision->time|time}</td>

						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}
