<?php

namespace SV\TitleEditHistory\Service\Base;

trait EditorTrait
{
    /** @var bool */
    protected $logEdit = true;
    /** @var null|string */
    protected $oldTitle = null;
    /** @var null|int */
    protected $logDelay = null;
    /** @var bool */
    protected $logHistory = true;

    /**
     * @param $logEdit
     */
    public function logEdit($logEdit)
    {
        $this->logEdit = $logEdit;
    }

    /**
     * @param $logDelay
     */
    public function logDelay($logDelay)
    {
        $this->logDelay = $logDelay;
    }

    /**
     * @param bool $logHistory
     */
    public function logHistory($logHistory)
    {
        $this->logHistory = $logHistory;
    }
}