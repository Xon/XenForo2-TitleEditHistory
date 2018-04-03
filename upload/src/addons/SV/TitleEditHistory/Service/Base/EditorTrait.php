<?php

namespace SV\TitleEditHistory\Service\Base;

use XF\Mvc\Entity\Entity;

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

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        /** @var Entity $content */
        $content = $this->getContent();
        /** @noinspection PhpUndefinedMethodInspection */
        $editKeys = $content->getTitleEditKeys();

        $oldTitle = $content->get($editKeys['title']);

        if (is_callable('parent::setTitle'))
        {
            parent::setTitle($title);
        }
        else
        {
            $content->set($editKeys['title'], $title);
        }

        if ($content->isChanged($editKeys['title']))
        {
            $this->setupEditHistory($oldTitle);
        }
    }

    /**
     * @param string $oldTitle
     */
    protected function setupEditHistory($oldTitle)
    {
        /** @var EditorInterface $this */
        $content = $this->getContent();
        $editKeys = $content->getTitleEditKeys();

        $options = \XF::app()->options();
        if ($options->editLogDisplay['enabled'] && $this->logEdit)
        {
            $delay = is_null($this->logDelay) ? $options->editLogDisplay['delay'] * 60 : $this->logDelay;
            if ($content->get($editKeys['edit_date']) + $delay <= \XF::$time)
            {
                $content->set($editKeys['edit_count'], $content->get($editKeys['edit_count']) + 1);
                $content->set($editKeys['last_edit_date'], \XF::$time);
                $content->set($editKeys['last_edit_user_id'], \XF::visitor()->user_id);
            }
        }

        if ($options->editHistory['enabled'] && $this->logHistory)
        {
            $this->oldTitle = $oldTitle;
        }
    }

    /**
     * @return \XF\Entity\Thread
     */
    protected function _save()
    {
        /** @var EditorInterface $this */
        $content = $this->getContent();
        $editKeys = $content->getTitleEditKeys();

        $visitor = \XF::visitor();

        $db = \XF::db();
        $db->beginTransaction();

        /** @noinspection PhpUndefinedClassInspection */
        $content = parent::_save();

        if ($this->oldTitle)
        {
            /** @var \XF\Repository\EditHistory $repo */
            $repo = $this->repository('XF:EditHistory');
            $repo->insertEditHistory($editKeys['content_type'], $content, $visitor, $this->oldTitle, \XF::app()->request()->getIp());
        }

        $db->commit();

        return $content;
    }
}