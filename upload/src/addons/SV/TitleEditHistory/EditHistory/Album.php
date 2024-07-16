<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\TitleEditHistory\EditHistory;

use XF\EditHistory\AbstractHandler;
use XF\Mvc\Entity\Entity;
use SV\TitleEditHistory\XFMG\Entity\Album as ExtendedAlbumEntity;

class Album extends AbstractHandler
{
    use EditTitleHistoryTrait;

    /**
     * @param Entity|ExtendedAlbumEntity $content
     *
     * @return string
     */
    public function getContentLink(Entity $content)
    {
        return \XF::app()->router('public')->buildLink('media/albums', $content);
    }

    /**
     * @return array
     */
    public function getEntityWith()
    {
        return ['User'];
    }
}