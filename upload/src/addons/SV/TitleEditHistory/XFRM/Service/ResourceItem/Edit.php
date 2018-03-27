<?php

namespace SV\TitleEditHistory\XFRM\Service\ResourceItem;

use SV\TitleEditHistory\Service\Base\EditorInterface;
use SV\TitleEditHistory\Service\Base\EditorTrait;
use SV\TitleEditHistory\XFRM\Entity\ResourceItem;
use XF\Mvc\Entity\Entity;

class Edit extends XFCP_Edit implements EditorInterface
{
    use EditorTrait;

    public function getContent()
    {
        return $this->resource;
    }
}