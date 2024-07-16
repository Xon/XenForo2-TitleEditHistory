<?php

namespace SV\TitleEditHistory\XFMG\Service\Media;

use SV\TitleEditHistory\Service\Base\EditorInterface;
use SV\TitleEditHistory\Service\Base\EditorTrait;

/**
 * @extends \XFMG\Service\Media\Editor
 */
class Editor extends XFCP_Editor implements EditorInterface
{
    use EditorTrait
    {
        setTitle as setupTitleForEditHistory;
    }

    /**
     * @param string $title
     * @param null   $description
     */
    public function setTitle($title, $description = null)
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->setupTitleForEditHistory($title, $description);
    }

    public function getContent()
    {
        return $this->getMediaItem();
    }
}