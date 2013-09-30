<?php
namespace cms\data\content;

use cms\data\content\ContentList;

class ViewableContentList extends ContentList{
    public $decoratorClassName = 'cms\data\content\ViewableContent';
}