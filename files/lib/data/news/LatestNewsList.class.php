<?php
namespace cms\data\news;

class LatestNewsList extends ViewableNewsList{
    
    public $sqlLimit = CMS_NEWS_LATEST_LIMIT;
    public $sqlOrderBy = 'news.time DESC';
}