<?php

namespace SV\TitleEditHistory\XFRM\Pub\Controller;

use SV\TitleEditHistory\Pub\Controller\TitleHistoryTrait;

class ResourceItem extends XFCP_ResourceItem
{
    use TitleHistoryTrait;

    /**
     * @return string|null
     * @throws \LogicException
     */
    protected function getTitleHistoryContentType()
    {
        return 'resource_title';
    }

    /**
     * @return string|null
     * @throws \LogicException
     */
    protected function getTitleHistoryContentIdKey()
    {
        return 'resource_id';
    }
}