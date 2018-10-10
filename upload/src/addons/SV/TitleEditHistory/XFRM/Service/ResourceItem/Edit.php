<?php

namespace SV\TitleEditHistory\XFRM\Service\ResourceItem;

use SV\TitleEditHistory\Service\Base\EditorInterface;
use SV\TitleEditHistory\Service\Base\EditorTrait;

class Edit extends XFCP_Edit implements EditorInterface
{
    use EditorTrait;

    public function getContent()
    {
        return $this->resource;
    }
}