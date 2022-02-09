<?php

namespace SV\TitleEditHistory\XF\Pub\Controller;

use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use SV\TitleEditHistory\Pub\Controller\TitleHistoryTrait;

class Thread extends XFCP_Thread
{
    use TitleHistoryTrait;

    protected function getTitleHistoryKeys()
    {
        /** @var IHistoryTrackedTitle $content */
        $content = $this->em()->create('XF:Thread');

        return $content->getTitleEditKeys();
    }
}
