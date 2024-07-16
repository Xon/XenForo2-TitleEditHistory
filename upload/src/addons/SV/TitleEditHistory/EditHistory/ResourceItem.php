<?php

namespace SV\TitleEditHistory\EditHistory;

use XF\EditHistory\AbstractHandler;
use XF\Mvc\Entity\Entity;

class ResourceItem extends AbstractHandler
{
    use EditTitleHistoryTrait;

    public function getContentLink(Entity $content): string
    {
        return \XF::app()->router('public')->buildLink('resources', $content);
    }

    public function getEntityWith(): array
    {
        $visitor = \XF::visitor();

        return ['Category', 'Category.Permissions|' . $visitor->permission_combination_id];
    }
}