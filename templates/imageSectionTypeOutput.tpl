<div class="image">
    {if $resizable == 1}
        <div class="attachmentThumbnail" >
        {if $link != ''}
           <a href="{$link}"><img src="{$__wcf->getPath('cms')}files/{$image->filename}"  alt="{$subtitle}"  title="{$subtitle}" class="jsTooltip" /></a>
        {else}
             <a href="{$__wcf->getPath('cms')}files/{$image->filename}" class="jsImageViewer"><img src="{$__wcf->getPath('cms')}files/{$image->filename}" alt="{$subtitle}"  title="{$subtitle}" class="jsTooltip" style="max-width: 280px; max-height: 210px;"/></a>
        {/if}
            {if $subtitle != ''}
            <div title="{$subtitle}">
                <p>{$subtitle}</p>
                <small>{$image->type} - {$image->size|filesize}</small>
                </div>
            {/if}
       </div>
    {else}
        {if $link != ''}
            <a href="{$link}"><img src="{$__wcf->getPath('cms')}files/{$image->filename}" alt="{$subtitle}"  title="{$subtitle}" class="jsTooltip jsResizeImage"/></a>
        {else}
            <img src="{$__wcf->getPath('cms')}files/{$image->filename}" alt="{$subtitle}"  title="{$subtitle}" class="jsTooltip jsResizeImage"/>
        {/if}
        {if $subtitle != ''}
            <div class="container containerPadding marginTop shadow caption" title="{$subtitle}">
                <p>{$subtitle}</p>
                <small>{$image->type} - {$image->size|filesize}</small>
                </div>
            {/if}
    {/if}   
</div>