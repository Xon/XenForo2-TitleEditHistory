<?php

namespace SV\TitleEditHistory\EditHistory;

use XF\EditHistory\AbstractHandler;
use XF\Entity\EditHistory;
use XF\Mvc\Entity\Entity;

class Thread extends AbstractHandler
{
    use EditTitleHistoryTrait;

    /**
     * @param \SV\TitleEditHistory\XF\Entity\Thread|Entity $content
     * @return string
     */
    public function getContentLink(Entity $content)
    {
        return \XF::app()->router('public')->buildLink('threads', $content);
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
