<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Search.User('#username');
		});
		//]]>
</script>

<dl>
	<dt><label for="username">{lang}cms.acp.content.type.de.codequake.cms.content.type.user.name{/lang}</label></dt>
	<dd>
		<input type="text" name="contentData[name]" id="username" class="medium" value="{if $contentData['name']|isset}{$contentData['name']}{/if}"/>
	</dd>
</dl>
