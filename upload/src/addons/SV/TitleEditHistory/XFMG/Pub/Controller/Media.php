<?php

namespace SV\TitleEditHistory\XFMG\Pub\Controller;

use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use SV\TitleEditHistory\Pub\Controller\TitleHistoryTrait;

/**
 * @extends  \XFMG\Pub\Controller\Media
 */
class Media extends XFCP_Media
{
    use TitleHistoryTrait;

    /**
     * @return mixed
     */
    protected function getTitleHistoryKeys()
    {
        /** @var IHistoryTrackedTitle $content */
        $content = $this->em()->create('XFMG:MediaItem');

        return $content->getTitleEditKeys();
    }
}