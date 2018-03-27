<?php

namespace SV\TitleEditHistory\EditHistory;

use XF\EditHistory\AbstractHandler;
use XF\Entity\EditHistory;
use XF\Mvc\Entity\Entity;

class ResourceItem extends AbstractHandler
{
    use EditTitleHistoryTrait;

    /**
     * @param \SV\TitleEditHistory\XFRM\Entity\ResourceItem|Entity $content
     * @return mixed|string
     */
    public function getContentLink(Entity $content)
    {
        return \XF::app()->router('public')->buildLink('resources', $content);
    }

    /**
     * @param \SV\TitleEditHistory\XFRM\Entity\ResourceItem|Entity $content
     * @param EditHistory                                          $history
     * @param EditHistory|null                                     $previous
     */
    public function revertToVersion(Entity $content, EditHistory $history, EditHistory $previous = null)
    {
        /** @var \SV\TitleEditHistory\XFRM\Service\ResourceItem\Edit $editor */
        $editor = \XF::app()->service('XFRM:ResourceItem\Edit', $content);

        $editor->logEdit(false);
        $editor->setTitle($history->old_text);

        if (!$previous || $previous->edit_user_id != $content->user_id)
        {
            $content->resource_title_last_edit_date = 0;
        }
        else if ($previous && $previous->edit_user_id === $content->user_id)
        {
            $content->resource_title_last_edit_date = $previous->edit_date;
            $content->resource_title_last_edit_user_id = $previous->edit_user_id;
        }
    }

    /**
     * @param \SV\TitleEditHistory\XFRM\Entity\ResourceItem|Entity $content
     * @return int
     */
    public function getEditCount(Entity $content)
    {
        return $content->resource_title_edit_count;
    }

    /**
     * @return array
     */
    public function getEntityWith()
    {
        $visitor = \XF::visitor();

        return ['Category', 'Category.Permissions|' . $visitor->permission_combination_id];
    }
}