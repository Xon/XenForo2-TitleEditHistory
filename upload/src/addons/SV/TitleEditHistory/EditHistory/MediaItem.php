<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\TitleEditHistory\EditHistory;

use SV\TitleEditHistory\XFMG\Entity\MediaItem as ExtendedMediaItemEntity;
use XF\EditHistory\AbstractHandler;
use XF\Mvc\Entity\Entity;

class MediaItem extends AbstractHandler
{
    use EditTitleHistoryTrait;

    /**
     * @param Entity|ExtendedMediaItemEntity $content
     *
     * @return string
     */
    public function getContentLink(Entity $content)
    {
        return \XF::app()->router('public')->buildLink('media', $content);
    }

    /**
     * @return array
     */
    public function getEntityWith()
    {
        return ['User'];
    }
}