<?php

namespace SV\TitleEditHistory\XFMG\Pub\Controller;

use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use SV\TitleEditHistory\Pub\Controller\TitleHistoryTrait;

/**
 * Class Album
 *
 * @package SV\TitleEditHistory\XFMG\Pub\Controller
 */
class Album extends XFCP_Album
{
    use TitleHistoryTrait;

    /**
     * @return mixed
     */
    protected function getTitleHistoryKeys()
    {
        /** @var IHistoryTrackedTitle $content */
        $content = $this->em()->create('XFMG:Album');

        return $content->getTitleEditKeys();
    }
}