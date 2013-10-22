<?php
namespace cms\data\news;
use wcf\data\AbstractDatabaseObjectAction;

class NewsAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\news\NewsEditor';
    protected $permissionsDelete = array('mod.cms.news.canModerateNews');
    
}