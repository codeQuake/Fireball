<script data-relocate="true" type="text/javascript" src="{@$__wcf->getPath()}js/WCF.Message.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
		//<![CDATA[
		$(function() {
			WCF.Message.Submit.registerButton('sectionData', $('.formSubmit > input[type=submit]'));
		});
		//]]>
</script>


<dl class="wide">
  <dt>
    <label for="sectionData">{lang}cms.acp.content.section.data.text{/lang}</label>
  </dt>
  <dd>
    <textarea id="text" name="text" rows="5" cols="40" class="long">{$i18nPlainValues['text']}</textarea>
  </dd>
</dl>
{include file='messageFormTabs' wysiwygContainerID='text'}
{include file='wysiwyg'}

{include file='multipleLanguageInputJavascript' elementIdentifier='text' forceSelection=false}