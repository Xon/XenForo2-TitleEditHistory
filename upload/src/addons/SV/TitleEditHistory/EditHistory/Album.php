<?php

namespace SV\TitleEditHistory\EditHistory;

use XF\EditHistory\AbstractHandler;
use XF\Mvc\Entity\Entity;

class Album extends AbstractHandler
{
    use EditTitleHistoryTrait;

    public function getContentLink(Entity $content): string
    {
        return \XF::app()->router('public')->buildLink('media/albums', $content);
    }

    public function getEntityWith(): array
    {
        return ['User'];
    }
}