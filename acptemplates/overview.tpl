{include file='header' pageTitle='cms.acp.page.overview'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.page.overview{/lang}</h1>
	<script data-relocate="true">
	    //<![CDATA[
	    $(function () {
	        new WCF.Action.Delete('cms\\data\\page\\PageAction', '.jsPageRow');
	        new WCF.Action.Delete('cms\\data\\content\\ContentAction', '.jsContentRow');
	        new WCF.Action.Delete('cms\\data\\content\\section\\ContentSectionAction', '.jsSectionRow');
	        
	    });
	    //]]>
	</script>
</header>


{if $objects|count}
<section id="pageList" class="container containerPadding sortableListContainer marginTop">
    <ol id="pageContainer0" class="sortableList" data-object-id="0">
         {foreach from=$objects item=page}
            <script data-relocate="true">
                //<![CDATA[
                $(function () {
                    $("#pageLabel{$page->pageID}").click(function () {
                        $("#contentContainer{$page->pageID}").toggle("slow");
                    });
                });
                    //]]>
	        </script>
            <li class="jsPageRow jsCollapsibleCategory marginTop" data-object-id="{$page->pageID}">
                <span id="pageLabel{$page->pageID}" class="sortableNodeLabel pointer">
                    <span id="button{$page->pageID}" class="collapsibleButton icon icon16 icon-file-text-alt"></span>
                    {@$page->getTitle()|language}
                    <span class="statusDisplay sortableButtonContainer">
                        <a href="{link controller='PageEdit' id=$page->pageID application='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							<span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$page->pageID}" data-confirm-message="{lang}cms.acp.page.delete.sure{/lang}"></span>
							<a href="{link controller='ContentList' id=$page->pageID application='cms'}{/link}" title="{lang}cms.acp.page.content.list{/lang}" class="jsTooltip"><span class="icon icon16 icon-file"></span></a>
                    </span>
                </span>
                <ol id="contentContainer{$page->pageID}" class="sortableList" data-object-id="{$page->pageID}" style="display:none;">
                    {foreach from=$page->getContentList('body') item=content}
                        <script data-relocate="true">
                            //<![CDATA[
                            $(function () {
                                $("#contentLabel{$content->contentID}").click(function () {
                                    $("#sectionContainer{$content->contentID}").toggle("slow");
                                });
                            });
                            //]]>
	                    </script>
                        <li class="jsContentRow" data-object-id="{$content->contentID}">
                            <span id="contentLabel{$content->contentID}" class="sortableNodeLabel pointer">
                                <span class="collapsibleButton icon icon16 icon-file"></span>
                                {@$content->getTitle()|language} <span class="badge">{$content->position}</span>
                            
                                <span class="statusDisplay sortableButtonContainer">
							        <a href="{link controller='ContentEdit' id=$content->contentID application='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
							        <span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$content->contentID}" data-confirm-message="{lang}cms.acp.content.delete.sure{/lang}"></span>
							        <a href="{link controller='ContentSectionAdd' id=$content->contentID application='cms'}{/link}" title="{lang}cms.acp.content.content.section.add{/lang}" class="jsTooltip"><span class="icon icon16 icon-plus-sign"></span></a>
							        <a href="{link controller='ContentSectionList' id=$content->contentID application='cms'}{/link}" title="{lang}cms.acp.content.content.section.list{/lang}" class="jsTooltip"><span class="icon icon16 icon-list-alt"></span></a>
						        </span>
                            </span>
                            <ol id="sectionContainer{$content->contentID}" class="sortableList" data-object-id="{$content->contentID}" style="display:none;">
                                {foreach from=$content->getSections() item=section}
                                    <li class="jsSectionRow  " data-object-id="{$section->sectionID}">
                                       <span class=" sortableNodeLabel">
                                            <span class="collapsibleButton icon icon16 icon-list-alt"></span>
                                            {@$section->getPreview()}
                                            <span class="statusDisplay sortableButtonContainer">
					                            <a href="{link controller='ContentSectionEdit' id=$section->sectionID application='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
					                            <span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$section->sectionID}" data-confirm-message="{lang}cms.acp.content.section.delete.sure{/lang}"></span>
				                            </span>
                                        </span>
                                    </li>
                                {/foreach}
                            </ol>
                        </li>
                    {/foreach}
                    {foreach from=$page->getContentList('sidebar') item=content}
                    <script data-relocate="true">
                        //<![CDATA[
                        $(function () {
                            $("#contentLabel{$content->contentID}").click(function () {
                                $("#sectionContainer{$content->contentID}").toggle("slow");
                            });
                        });
                        //]]>
	                    </script>
                        <li class="jsContentRow" data-object-id="{$content->contentID}">
                            <span id="contentLabel{$content->contentID}" class=" sortableNodeLabel pointer">
                                <span class="collapsibleButton icon icon16 icon-file"></span>
                                {@$content->getTitle()|language} <span class="badge">{$content->position}</span>
                            </span>
                            <ol id="sectionContainer{$content->contentID}" class="sortableList" data-object-id="{$content->contentID}" style="display:none;">
                                {foreach from=$content->getSections() item=section}
                                    <li class="jsSectionRow" data-object-id="{$section->sectionID}">
                                        <span class=" sortableNodeLabel">
                                         <span class="collapsibleButton icon icon16 icon-list-alt"></span>
                                         {@$section->getPreview()}
                                            <span class="statusDisplay sortableButtonContainer">
					                            <a href="{link controller='ContentSectionEdit' id=$section->sectionID application='cms'}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
					                            <span class="icon icon16 icon-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$section->sectionID}" data-confirm-message="{lang}cms.acp.content.section.delete.sure{/lang}"></span>
				                            </span>
                                        </span>

                                    </li>
                                {/foreach}
                            </ol>
                        </li>
                    {/foreach}
                </ol>
            </li>
         {/foreach}
    </ol>
 </section>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}