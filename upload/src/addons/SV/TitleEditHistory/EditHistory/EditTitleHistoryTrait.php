<?php

namespace SV\TitleEditHistory\EditHistory;

use XF\Mvc\Entity\Entity;

trait EditTitleHistoryTrait
{
    /**
     * @param Entity $content
     * 
     * @return bool
     */
    public function canViewHistory(Entity $content)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $content->canViewTitleHistory() && $content->canView();
    }

    /**
     * @param Entity $content
     *
     * @return bool
     */
    public function canRevertContent(Entity $content)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $content->canEdit();
    }

    /**
     * @param Entity $content
     *
     * @return string
     */
    public function getContentTitle(Entity $content)
    {
        $prefix = '';

        try
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $prefixIds = $content->sv_prefix_ids;
            $prefixes = [];
            foreach ($prefixIds AS $prefixId)
            {
                $prefixes[] = '[' . \XF::phrase($content->getEntityContentType() . '_prefix.' . $prefixId, [], false)->render() . ']';
            }
            $prefix = implode(" ", $prefixes) . ' ';
        }
        catch (\InvalidArgumentException $e)
        {
            try
            {
                $prefix = $content->getRelation('Prefix') ? "[" . $content->getRelation('Prefix')->getTitle() . "] " : "";
            }
            catch (\InvalidArgumentException $e)
            {}
        }

        /** @noinspection PhpUndefinedFieldInspection */
        return $prefix . $content->title;
    }

    /**
     * @param Entity $content
     *
     * @return string
     */
    public function getContentText(Entity $content)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return $content->title;
    }

    /**
     * @param Entity $content
     *
     * @return array
     */
    public function getBreadcrumbs(Entity $content)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $content->getBreadcrumbs();
    }

    /**
     * @param $text
     * @param Entity|null $content
     *
     * @return string
     */
    public function getHtmlFormattedContent($text, /** @noinspection PhpUnusedParameterInspection */
                                            Entity $content = null)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
    }
}