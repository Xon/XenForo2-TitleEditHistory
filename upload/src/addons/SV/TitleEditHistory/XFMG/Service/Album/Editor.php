<?php

namespace SV\TitleEditHistory\XFMG\Service\Album;

use SV\TitleEditHistory\Service\Base\EditorInterface;
use SV\TitleEditHistory\Service\Base\EditorTrait;

/**
 * @extends  \XFMG\Service\Album\Editor
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
        return $this->getAlbum();
    }
}