{if $resizable == 1}
    <ul>
      {foreach from=$images item=image}
      <li class="attachmentThumbnail" >
        {if $link != ''}
        <a {if EXTERNAL_LINK_TARGET_BLANK}target="_blank"{/if} href="{$link}">
        <img src="{$__wcf->getPath('cms')}files/{if $image['folder']->folderPath != ''}{$image['folder']->folderPath}/{/if}{$image['file']->filename}"  alt="{$subtitle}"  title="{$subtitle}" class="jsTooltip" />
        </a>
        {else}
        <a href="{$__wcf->getPath('cms')}files/{if $image['folder']->folderPath != ''}{$image['folder']->folderPath}/{/if}{$image['file']->filename}" class="jsImageViewer"><img src="{$__wcf->getPath('cms')}files/{$image['file']->filename}"  alt="{$subtitle}"  title="{$subtitle}"  style="max-width: {ATTACHMENT_THUMBNAIL_WIDTH}px; max-height: {ATTACHMENT_THUMBNAIL_HEIGHT}px;" /></a>
        {/if}
        {if $subtitle != ''}
            <div title="{$subtitle}">
                <p>{$subtitle}</p>
          </div>
        {/if}
      </li>
      {/foreach}
    </ul>
{else}
  <ul>
    {foreach from=$images item=image}
    <li>
      {if $link != ''}
      <a {if EXTERNAL_LINK_TARGET_BLANK}target="_blank"{/if} href="{$link}">
      <img src="{$__wcf->getPath('cms')}files/{if $image['folder']->folderPath != ''}{$image['folder']->folderPath}/{/if}{$image['file']->filename}"  alt="{$subtitle}"  title="{$subtitle}" class="jsTooltip jsResizeImage" />
      </a>
      {else}
      <img src="{$__wcf->getPath('cms')}files/{if $image['folder']->folderPath != ''}{$image['folder']->folderPath}/{/if}{$image['file']->filename}"  alt="{$subtitle}"  title="{$subtitle}" class="jsTooltip jsResizeImage" />
      {/if}
      {if $subtitle != ''}
      <div title="{$subtitle}" class="caption shadow">
        <small>{$subtitle}</small>
      </div>
    {/if}
  </li>
    {/foreach}
  </ul>
{/if}