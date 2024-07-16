<?php

namespace SV\TitleEditHistory\XF\Pub\Controller;

use SV\StandardLib\Helper;
use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use SV\TitleEditHistory\Pub\Controller\TitleHistoryTrait;

/**
 * @extends \XF\Pub\Controller\Thread
 */
class Thread extends XFCP_Thread
{
    use TitleHistoryTrait;

    protected function getTitleHistoryKeys(): array
    {
        /** @var IHistoryTrackedTitle $content */
        $content = Helper::createEntity(\XF\Entity\Thread::class);

        return $content->getTitleEditKeys();
    }
}
