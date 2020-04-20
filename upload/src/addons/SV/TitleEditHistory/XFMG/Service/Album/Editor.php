<?php

namespace SV\TitleEditHistory\XFMG\Service\Album;

use SV\TitleEditHistory\Service\Base\EditorInterface;
use SV\TitleEditHistory\Service\Base\EditorTrait;
use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;

/**
 * Class Editor
 *
 * @package SV\TitleEditHistory\XFMG\Service\Album
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
        $this->setupTitleForEditHistory($title, $description);
    }

    /**
     * @return IHistoryTrackedTitle|\XFMG\Entity\Album
     */
    public function getContent()
    {
        return $this->getAlbum();
    }
}