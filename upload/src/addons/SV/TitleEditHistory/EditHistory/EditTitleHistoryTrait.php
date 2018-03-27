<?php

namespace SV\TitleEditHistory\EditHistory;

use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use SV\TitleEditHistory\Service\Base\EditorInterface;
use XF\Entity\EditHistory;
use XF\Mvc\Entity\Entity;

trait EditTitleHistoryTrait
{
    /**
     * @param IHistoryTrackedTitle|Entity $content
     * @return bool
     */
    public function canViewHistory(Entity $content)
    {
        return $content->canViewTitleHistory() && $content->canView();
    }

    /**
     * @param IHistoryTrackedTitle|Entity $content
     * @return bool
     */
    public function canRevertContent(Entity $content)
    {
        return $content->canEditTitle();
    }

    /**
     * @param IHistoryTrackedTitle|Entity $content
     * @param EditHistory                 $history
     * @param EditHistory|null            $previous
     * @return mixed
     */
    public function revertToVersion(Entity $content, EditHistory $history, EditHistory $previous = null)
    {
        $editKeys = $content->getTitleEditKeys();

        /** @var \XF\Service\AbstractService|EditorInterface $editor */
        $editor = \XF::app()->service($editKeys['editor'], $content);

        $editor->logEdit(false);
        $editor->setTitle($history->old_text);

        if (!$previous || $previous->edit_user_id != $content->user_id)
        {
            // if previous is a mod edit, don't show as it may have been hidden
            $content->set($editKeys['edit_count'], 0);
        }
        else if ($previous && $previous->edit_user_id == $content->user_id)
        {
            $content->set($editKeys['last_edit_date'], $previous->edit_date);
            $content->set($editKeys['last_edit_user_id'], $previous->edit_user_id);
        }

        return $editor->save();
    }

    /**
     * @param IHistoryTrackedTitle|Entity $content
     * @return string
     */
    public function getContentTitle(Entity $content)
    {
        $prefix = '';

        if (isset($content->sv_prefix_ids))
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
        else if (isset($content->Prefix))
        {
            $prefix = $content->getRelation('Prefix') ? "[" . $content->getRelation('Prefix')->getTitle() . "] " : "";
        }

        $editKeys = $content->getTitleEditKeys();

        return $prefix . $content->get($editKeys['title']);
    }

    /**
     * @param IHistoryTrackedTitle|Entity $content
     * @return int
     */
    public function getEditCount(Entity $content)
    {
        $editKeys = $content->getTitleEditKeys();

        return $content->get($editKeys['edit_count']);
    }

    /**
     * @param IHistoryTrackedTitle|Entity $content
     * @return string
     */
    public function getContentText(Entity $content)
    {
        $editKeys = $content->getTitleEditKeys();

        return $content->get($editKeys['title']);
    }

    /**
     * @param IHistoryTrackedTitle|Entity $content
     * @return array
     */
    public function getBreadcrumbs(Entity $content)
    {
        if (!is_callable([$content, 'getBreadcrumbs']))
        {
            return [];
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return $content->getBreadcrumbs();
    }

    /**
     * @param             $text
     * @param Entity|null $content
     * @return string
     */
    public function getHtmlFormattedContent($text, /** @noinspection PhpUnusedParameterInspection */
                                            Entity $content = null)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
    }
}