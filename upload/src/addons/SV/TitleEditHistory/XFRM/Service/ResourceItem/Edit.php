<?php

namespace SV\TitleEditHistory\XFRM\Service\ResourceItem;

use SV\TitleEditHistory\XFRM\Entity\ResourceItem;
use SV\TitleEditHistory\Service\Base\EditorTrait;

class Edit extends XFCP_Edit
{
    use EditorTrait;

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $oldTitle = $this->resource->title;

        $upstream = parent::setTitle($title);

        if ($this->resource->isChanged('title'))
        {
            $this->setupEditHistory($oldTitle);
        }

        return $upstream;
    }

    /**
     * @param string $oldTitle
     */
    protected function setupEditHistory($oldTitle)
    {
        /** @var ResourceItem $resource */
        $resource = $this->resource;

        $options = $this->app->options();
        if ($options->editLogDisplay['enabled'] && $this->logEdit)
        {
            $delay = is_null($this->logDelay) ? $options->editLogDisplay['delay'] * 60 : $this->logDelay;
            if ($resource->resource_date + $delay <= \XF::$time)
            {
                $resource->resource_title_edit_count++;
                $resource->resource_title_last_edit_date = \XF::$time;
                $resource->resource_title_last_edit_user_id = \XF::visitor()->user_id;
            }
        }

        if ($options->editHistory['enabled'] && $this->logHistory)
        {
            $this->oldTitle = $oldTitle;
        }
    }

    /**
     * @return ResourceItem
     */
    protected function _save()
    {
        $visitor = \XF::visitor();

        $db = $this->db();
        $db->beginTransaction();

        $resource = parent::_save();

        if ($this->oldTitle)
        {
            /** @var \XF\Repository\EditHistory $repo */
            $repo = $this->repository('XF:EditHistory');
            $repo->insertEditHistory('resource_title', $resource, $visitor, $this->oldTitle, $this->app->request()->getIp());
        }

        $db->commit();

        return $resource;
    }
}