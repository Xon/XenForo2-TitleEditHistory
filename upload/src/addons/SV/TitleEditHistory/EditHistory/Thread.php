<?php

namespace SV\TitleEditHistory\EditHistory;

use XF\EditHistory\AbstractHandler;
use XF\Mvc\Entity\Entity;

class Thread extends AbstractHandler
{
    use EditTitleHistoryTrait;

    public function getContentLink(Entity $content): string
    {
        return \XF::app()->router('public')->buildLink('threads', $content);
    }

    public function getEntityWith(): array
    {
        $visitor = \XF::visitor();

        return ['Forum', 'Forum.Node.Permissions|' . $visitor->permission_combination_id];
    }
}
