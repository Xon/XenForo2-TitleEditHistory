<?php

namespace SV\TitleEditHistory\Service\Base;

use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use XF\Mvc\Entity\Entity;

interface EditorInterface
{
    /**
     * @param bool $logEdit
     */
    public function logEdit($logEdit);

    /**
     * @param int|null $logDelay
     */
    public function logDelay($logDelay);

    /**
     * @param bool $logHistory
     */
    public function logHistory($logHistory);

    /**
     * @param string $title
     */
    public function setTitle($title);


    public function save();

    /** @noinspection PhpDocSignatureInspection */
    /**
     * @return Entity|IHistoryTrackedTitle
     */
    public function getContent();
}