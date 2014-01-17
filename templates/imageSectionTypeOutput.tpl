{if $resizable == 1}
    <ul>
      {foreach from=$images item=image}
      <li class="attachmentThumbnail" >
          <img src="{$__wcf->getPath('cms')}files/{$image->filename}"  alt="{$subtitle}"  title="{$subtitle}" class="jsTooltip" />
      </li>
      {/foreach}
    </ul>
{else}
<ul>
  {foreach from=$images item=image}
    <li>
      <img src="{$__wcf->getPath('cms')}files/{$image->filename}" alt="{$subtitle}"  title="{$subtitle}" class="jsTooltip jsResizeImage"/>
    </li>
  {/foreach}
</ul>
{/if}