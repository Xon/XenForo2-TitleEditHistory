<?php

namespace SV\TitleEditHistory\XFRM\Pub\Controller;

use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use SV\TitleEditHistory\Pub\Controller\TitleHistoryTrait;

/**
 * @extends \XFRM\Pub\Controller\ResourceItem
 */
class ResourceItem extends XFCP_ResourceItem
{
    use TitleHistoryTrait;

    protected function getTitleHistoryKeys()
    {
        /** @var IHistoryTrackedTitle $content */
        $content = $this->em()->create('XFRM:ResourceItem');

        return $content->getTitleEditKeys();
    }
}