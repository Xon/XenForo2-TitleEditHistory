<?php

namespace SV\TitleEditHistory\EditHistory;

use XF\EditHistory\AbstractHandler;
use XF\Mvc\Entity\Entity;

class MediaItem extends AbstractHandler
{
    use EditTitleHistoryTrait;

    public function getContentLink(Entity $content): string
    {
        return \XF::app()->router('public')->buildLink('media', $content);
    }

    public function getEntityWith(): array
    {
        return ['User'];
    }
}