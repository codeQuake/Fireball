<?php
namespace cms\data\news;
use wcf\data\DatabaseObjectDecorator;

class ViewableNews extends DatabaseObjectDecorator{
    protected static $baseClass = 'cms\data\news\News';

}