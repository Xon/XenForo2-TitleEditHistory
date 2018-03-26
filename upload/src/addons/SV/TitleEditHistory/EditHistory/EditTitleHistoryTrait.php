<?php

namespace SV\TitleEditHistory\EditHistory;

use XF\Mvc\Entity\Entity;

trait EditTitleHistoryTrait
{
    /**
     * @param Entity $content
     * @return bool
     */
    public function canViewHistory(Entity $content)
    {
        return $content->canViewTitleHistory() && $content->canView();
    }

    /**
     * @param Entity $content
     * @return bool
     */
    public function canRevertContent(Entity $content)
    {
        return $content->canEdit();
    }

    /**
     * @param Entity $content
     * @return string
     */
    public function getContentTitle(Entity $content)
    {
        $prefix = '';

        try
        {
            $prefix = $content->getRelation('Prefix') ? "[" . $content->getRelation('Prefix')->getTitle() . "] " : "";
        }
        catch (\InvalidArgumentException $e)
        {}

        return $prefix . $content->title;
    }

    /**
     * @param Entity $content
     * @return string
     */
    public function getContentText(Entity $content)
    {
        return $content->title;
    }

    /**
     * @param Entity $content
     * @return array
     */
    public function getBreadcrumbs(Entity $content)
    {
        return $content->getBreadcrumbs();
    }

    public function getHtmlFormattedContent($text, Entity $content = null)
    {
        return htmlspecialchars($text);
    }
}