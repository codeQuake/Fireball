{capture assign='pageTitle'}{lang}cms.acp.feed.{@$action}{/lang}{/capture}
{include file='header'}

<header class="boxHeadline">
    <h1>{lang}cms.acp.feed.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}
<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}


<div class="contentNavigation">
    <nav>
        <ul>
            <li><a href="{link application='cms' controller='FeedList'}{/link}" title="{lang}cms.acp.menu.link.cms.feed.list{/lang}" class="button"><span class="icon icon24 icon-list"></span> <span>{lang}cms.acp.menu.link.cms.feed.list{/lang}</span></a></li>
            {event name='contentNavigationButtons'}
        </ul>
    </nav>
</div>

<form method="post" action="{if $action == 'add'}{link application='cms' controller='FeedAdd'}{/link}{else}{link application='cms' controller='FeedEdit' id=$feedID}{/link}{/if}">
    <div class="marginTop container shadow containerPadding">
        <fieldset>
            <legend>{lang}cms.acp.feed.general{/lang}</legend>
            <dl>
                <dt><label for="title">{lang}cms.acp.feed.title{/lang}</label></dt>
                <dd>
                    <input type="text"  id="title" name="title" value="{$title}"/>
                </dd>
            </dl>
			<dl>
				<dt><label for="feedUrl">{lang}cms.acp.feed.url{/lang}</label></dt>
				<dd>
				<input type="text"  id="feedUrl" name="feedUrl" value="{$feedUrl}"/>
				</dd>
			</dl>
				{include file='messageFormMultilingualism'}
			<dl>
				<dt><label for="categoryID">{lang}cms.acp.feed.categoryID{/lang}</label></dt>
				<dd>
					<select id="categoryID" name="categoryID">
							{foreach from=$categories item=$category}
							<option value="{$category->categoryID}" {if $categoryID == $category->categoryID}selected="selected"{/if}>{$category->getTitle()|language}</option>
							{/foreach}
					</select>
				</dd>
			</dl>
			<dl>
					<dt><label for="text">{lang}cms.news.image{/lang}</label></dt>
					<dd>
						<div id="previewImage">
						{if $image|isset &&  $image->imageID && $image->imageID != 0}
								<div class="box96">
									<div class="framed">
										{@$image->getImageTag('96')}
									</div>
									<div>										<div>
											<p>{$image->title}</p>
										</div>
									</div>
								</div>
						{/if}
						</div>
						<a class="button" id="imageSelectButton">{lang}cms.news.image.select{/lang}</a>
						<script data-relocate="true">
							//<![CDATA[
							$(function() {
								WCF.Language.addObject({
										'cms.news.image.select': '{lang}cms.news.image.select{/lang}'
										});
								$('#imageSelect').hide();
								$('#imageSelectButton').click(function() {
									$('#imageSelect').wcfDialog({
										title: WCF.Language.get('cms.news.image.select')
									});
								});
							});
							//]]>
						</script>
						
						<input type="hidden" name="imageID" value="{$imageID}" id="imageID" />
						<div id="imageSelect" style="display: none;">
							{foreach from=$imageList item='imageItem'}
								<a id="imageSelect{$imageItem->imageID}">
									{@$imageItem->getImageTag('256')}
								</a>
								<script data-relocate="true">
									//<![CDATA[
									$(function() {
										$('#imageSelect{$imageItem->imageID}').click(function() {
											$('#imageID').val("{$imageItem->imageID}");
											var html = '<div class="box96"><div class="framed">{@$imageItem->getImageTag('96')}</div><div><p>{$imageItem->title}</p></div></div>';
											$('#previewImage').html(html);
											$('#imageSelect').wcfDialog('close');
										});
									});
									//]]>
								</script>
							{/foreach}
						</div>
					</dd>
				</dl>
        </fieldset>
        
    </div>
    <div class="formSubmit">
				<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
				 {@SECURITY_TOKEN_INPUT_TAG}
				<input type="hidden" name="action" value="{@$action}" />
			</div>
</form>

{include file='footer'}