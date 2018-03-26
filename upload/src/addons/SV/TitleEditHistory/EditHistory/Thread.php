<?php

namespace SV\TitleEditHistory\EditHistory;

use XF\EditHistory\AbstractHandler;
use XF\Entity\EditHistory;
use XF\Mvc\Entity\Entity;

class Thread extends AbstractHandler
{

    /**
     * @param \SV\TitleEditHistory\XF\Entity\Thread|Entity $content
     * @return bool
     */
    public function canViewHistory(Entity $content)
    {
        return $content->canViewTitleHistory() && $content->canView();
    }

    /**
     * @param \SV\TitleEditHistory\XF\Entity\Thread|Entity $content
     * @return bool
     */
    public function canRevertContent(Entity $content)
    {
        return $content->canEdit();
    }

    /**
     * @param \SV\TitleEditHistory\XF\Entity\Thread|Entity $content
     * @return string
     */
    public function getContentTitle(Entity $content)
    {
        $prefix = $content->getRelation('Prefix') ? "[" . $content->getRelation('Prefix')->getTitle() . "]" : "";

        return $prefix . ' ' . $content->title;
    }

    /**
     * @param \SV\TitleEditHistory\XF\Entity\Thread|Entity $content
     * @return string
     */
    public function getContentText(Entity $content)
    {
        return $content->title;
    }

    public function getContentLink(Entity $content)
    {
        return \XF::app()->router('public')->buildLink('threads', $content);
    }

    /**
     * @param \SV\TitleEditHistory\XF\Entity\Thread|Entity $content
     * @return array
     */
    public function getBreadcrumbs(Entity $content)
    {
        return $content->getBreadcrumbs();
    }

    /**
     * @param \SV\TitleEditHistory\XF\Entity\Thread|Entity $content
     * @param EditHistory                                  $history
     * @param EditHistory|null                             $previous
     * @return mixed
     */
    public function revertToVersion(Entity $content, EditHistory $history, EditHistory $previous = null)
    {
        /** @var \SV\TitleEditHistory\XF\Service\Thread\Editor $editor */
        $editor = \XF::app()->service('XF:Thread\Editor', $content);

        $editor->logEdit(false);
        $editor->setTitle($history->old_text);

        if (!$previous || $previous->edit_user_id != $content->user_id)
        {
            // if previous is a mod edit, don't show as it may have been hidden
            $content->thread_title_last_edit_date = 0;
        }
        else if ($previous && $previous->edit_user_id == $content->user_id)
        {
            $content->thread_title_last_edit_date = $previous->edit_date;
            $content->thread_title_last_edit_user_id = $previous->edit_user_id;
        }

        return $editor->save();
    }

    public function getHtmlFormattedContent($text, Entity $content = null)
    {
        return htmlspecialchars($text);
    }

    /**
     * @param \SV\TitleEditHistory\XF\Entity\Thread|Entity $content
     * @return mixed|null
     */
    public function getEditCount(Entity $content)
    {
        return $content->get('thread_title_edit_count');
    }

    /**
     * @return array
     */
    public function getEntityWith()
    {
        $visitor = \XF::visitor();

        return ['Forum', 'Forum.Node.Permissions|' . $visitor->permission_combination_id];
    }
}
