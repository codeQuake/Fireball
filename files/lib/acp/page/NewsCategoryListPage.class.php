<?php
namespace cms\acp\page;
use wcf\acp\page\AbstractCategoryListPage;


class NewsCategoryListPage extends AbstractCategoryListPage {

    public $activeMenuItem = 'cms.acp.menu.link.cms.news.category.list';

    public $objectTypeName = 'de.codequake.cms.category.news';

    public $pageTitle = 'wcf.category.list';
}
