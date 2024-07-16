<?php

namespace SV\TitleEditHistory\XFRM\Pub\Controller;

use SV\StandardLib\Helper;
use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use SV\TitleEditHistory\Pub\Controller\TitleHistoryTrait;

/**
 * @extends \XFRM\Pub\Controller\ResourceItem
 */
class ResourceItem extends XFCP_ResourceItem
{
    use TitleHistoryTrait;

    protected function getTitleHistoryKeys(): array
    {
        /** @var IHistoryTrackedTitle $content */
        $content = Helper::createEntity(\XFRM\Entity\ResourceItem::class);

        return $content->getTitleEditKeys();
    }
}