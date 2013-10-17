<div class="box32">
    <a href="{link controller='FileDownload' object=$file application='linklist'}{/link}">
        <span class="icon icon32 icon-paper-clip"></span>
    </a>
    <div>
        <p><a href="{link controller='FileDownload' object=$file application='linklist'}{/link}">{$file->getTitle()}</a></p>
        <small>{lang}cms.content.file.details{/lang}</small>
    </div>
</div>