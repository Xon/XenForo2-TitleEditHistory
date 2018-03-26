<?php

namespace SV\TitleEditHistory\EditHistory;

use XF\EditHistory\AbstractHandler;
use XF\Entity\EditHistory;
use XF\Mvc\Entity\Entity;

class ResourceItem extends AbstractHandler
{
    /**
     * @param \SV\TitleEditHistory\XFRM\Entity\ResourceItem|Entity $content
     * @return bool
     */
    public function canViewHistory(Entity $content)
    {
        return $content->canViewTitleHistory() && $content->canView();
    }

    /**
     * @param \SV\TitleEditHistory\XFRM\Entity\ResourceItem|Entity $content
     * @return mixed
     */
    public function canRevertContent(Entity $content)
    {
        return $content->canEdit();
    }

    /**
     * @param \SV\TitleEditHistory\XFRM\Entity\ResourceItem|Entity $content
     * @return string
     */
    public function getContentTitle(Entity $content)
    {
        $prefix = $content->getRelation('Prefix') ? "[" . $content->getRelation('Prefix')->getTitle() . "]" : "";

        return $prefix . ' ' . $content->title;
    }

    /**
     * @param \SV\TitleEditHistory\XFRM\Entity\ResourceItem|Entity $content
     * @return mixed|null
     */
    public function getContentText(Entity $content)
    {
        return $content->title;
    }

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
     * @return mixed
     */
    public function getBreadcrumbs(Entity $content)
    {
        return $content->getBreadcrumbs();
    }

    /**
     * @param \SV\TitleEditHistory\XFRM\Entity\ResourceItem|Entity $content
     * @param EditHistory                                          $history
     * @param EditHistory|null                                     $previous
     */
    public function revertToVersion(Entity $content, \XF\Entity\EditHistory $history, \XF\Entity\EditHistory $previous = null)
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
            $content->resource_title_last_date = $previous->edit_date;
            $content->resource_title_last_edit_user_id = $previous->edit_user_id;
        }
    }

    /**
     * @param                                                           $text
     * @param \SV\TitleEditHistory\XFRM\Entity\ResourceItem|Entity|null $content
     * @return string
     */
    public function getHtmlFormattedContent($text, Entity $content = null)
    {
        return htmlspecialchars($text);
    }

    /**
     * @param \SV\TitleEditHistory\XFRM\Entity\ResourceItem|Entity $content
     * @return mixed|null
     */
    public function getEditCount(Entity $content)
    {
        return $content->get('resource_title_edit_count');
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