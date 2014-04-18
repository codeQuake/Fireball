<?php
namespace cms\data\content\section;

/**
 *
 * @author Jens Krumsieck
 * @copyright 2014 codeQuake
 * @license GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package de.codequake.cms
 */
class ContentContentSectionList extends ViewableContentSectionList {
    public $contentID = 0;
    public $sqlOrderBy = 'content_section.showOrder ASC';

    public function __construct($contentID)
    {
        $this->contentID = $contentID;
        parent::__construct();
        
        $this->getConditionBuilder()->add('content_section.contentID = ?', array(
            $this->contentID
        ));
    }
}
