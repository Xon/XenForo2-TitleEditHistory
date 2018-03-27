<?php

namespace SV\TitleEditHistory\XF\Pub\Controller;

use SV\TitleEditHistory\Pub\Controller\TitleHistoryTrait;

class Thread extends XFCP_Thread
{
    use TitleHistoryTrait;

    /**
     * @return string|null
     * @throws \LogicException
     */
    protected function getTitleHistoryContentType()
    {
        return 'thread_title';
    }

    /**
     * @return string|null
     * @throws \LogicException
     */
    protected function getTitleHistoryContentIdKey()
    {
        return 'thread_id';
    }
}
