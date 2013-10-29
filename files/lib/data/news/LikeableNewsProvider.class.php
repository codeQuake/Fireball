<?php
namespace cms\data\news;
use wcf\data\like\object\ILikeObject;
use wcf\data\like\ILikeObjectTypeProvider;
use wcf\data\object\type\AbstractObjectTypeProvider;

class LikeableNewsProvider extends AbstractObjectTypeProvider implements ILikeObjectTypeProvider {

	public $className = 'cms\data\news\News';
	public $decoratorClassName = 'cms\data\news\LikeableNews';
    public $listClassName = 'cms\data\news\NewsList';
    public function checkPermissions(ILikeObject $object) {
		return $object->canRead();
	}
}
