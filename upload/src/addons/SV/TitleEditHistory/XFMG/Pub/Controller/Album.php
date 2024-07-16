<?php

namespace SV\TitleEditHistory\XFMG\Pub\Controller;

use SV\StandardLib\Helper;
use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use SV\TitleEditHistory\Pub\Controller\TitleHistoryTrait;

/**
 * @extends  \XFMG\Pub\Controller\Album
 */
class Album extends XFCP_Album
{
    use TitleHistoryTrait;

    protected function getTitleHistoryKeys(): array
    {
        /** @var IHistoryTrackedTitle $content */
        $content = Helper::createEntity(\XFMG\Entity\Album::class);

        return $content->getTitleEditKeys();
    }
}