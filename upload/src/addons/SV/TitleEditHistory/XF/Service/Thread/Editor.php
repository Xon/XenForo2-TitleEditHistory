<?php

namespace SV\TitleEditHistory\XF\Service\Thread;

use SV\TitleEditHistory\Service\Base\EditorInterface;
use SV\TitleEditHistory\Service\Base\EditorTrait;

/**
 * @Extends \XF\Service\Thread\Editor
 */
class Editor extends XFCP_Editor implements EditorInterface
{
    use EditorTrait;

    public function getContent()
    {
        return $this->thread;
    }
}
