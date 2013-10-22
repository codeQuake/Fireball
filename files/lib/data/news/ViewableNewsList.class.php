<?php
namespace cms\data\news;

use cms\data\news\NewsList;

class ViewableNewsList extends NewsList{
    public $decoratorClassName = 'cms\data\news\ViewableNews';
}