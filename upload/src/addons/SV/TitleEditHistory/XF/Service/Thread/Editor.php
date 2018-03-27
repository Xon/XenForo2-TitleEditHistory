<?php

namespace SV\TitleEditHistory\XF\Service\Thread;

use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use SV\TitleEditHistory\Service\Base\EditorInterface;
use SV\TitleEditHistory\Service\Base\EditorTrait;
use SV\TitleEditHistory\XF\Entity\Thread;

/**
 * Extends \XF\Service\Thread\Editor
 */
class Editor extends XFCP_Editor implements EditorInterface
{
    use EditorTrait;

    /**
     * @return \XF\Entity\Thread|\XF\Mvc\Entity\Entity|IHistoryTrackedTitle
     */
    public function getContent()
    {
        return $this->thread;
    }
}
