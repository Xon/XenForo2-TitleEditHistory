<?php

namespace SV\TitleEditHistory\EditHistory;

use XF\EditHistory\AbstractHandler;
use XF\Entity\EditHistory;
use XF\Mvc\Entity\Entity;

class ResourceItem extends AbstractHandler
{
    use EditTitleHistoryTrait;

    /**
     * @param \SV\TitleEditHistory\XFRM\Entity\ResourceItem|Entity $content
     * @return string
     */
    public function getContentLink(Entity $content)
    {
        return \XF::app()->router('public')->buildLink('resources', $content);
    }

    /**
     * @return array
     */
    public function getEntityWith()
    {
        $visitor = \XF::visitor();

        return ['Category', 'Category.Permissions|' . $visitor->permission_combination_id];
    }
}