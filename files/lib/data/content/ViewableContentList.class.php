<?php
namespace cms\data\Content;

use cms\data\content\ContentList;

class ViewableContentList extends ContentList{
    public $decoratorClassName = 'cms\data\content\ViewableContent';
}