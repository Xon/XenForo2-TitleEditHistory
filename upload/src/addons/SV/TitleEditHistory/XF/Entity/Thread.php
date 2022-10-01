<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\TitleEditHistory\XF\Entity;

use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use XF\Mvc\Entity\Structure;

/**
 * @property int thread_title_last_edit_date
 * @property int thread_title_last_edit_user_id
 * @property int thread_title_edit_count
 */
class Thread extends XFCP_Thread implements IHistoryTrackedTitle
{
    public function getTitleEditKeys()
    {
        return [
            'edit_date'         => 'post_date',
            'last_edit_date'    => 'thread_title_last_edit_date',
            'last_edit_user_id' => 'thread_title_last_edit_user_id',
            'edit_count'        => 'thread_title_edit_count',
            'content_type'      => 'thread_title',
            'content_id'        => 'thread_id',
            'title'             => 'title',
            'editor'            => 'XF:Thread\Editor',
        ];
    }

    /**
     * @return int
     */
    public function getTitleEditCount()
    {
        return $this->thread_title_edit_count;
    }

    /**
     * @param string|null $error
     * @return bool
     */
    public function canViewTitleHistory(&$error = null)
    {
        if (!\XF::visitor()->user_id)
        {
            return false;
        }

        if (!\XF::options()->editHistory['enabled'])
        {
            return false;
        }

        if (\XF::visitor()->hasNodePermission($this->node_id, 'manageAnyThread'))
        {
            return true;
        }

        return false;
    }

    /**
     * @param Structure $structure
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->columns['thread_title_last_edit_date'] = ['type' => self::UINT, 'default' => 0];
        $structure->columns['thread_title_last_edit_user_id'] = ['type' => self::UINT, 'default' => 0];
        $structure->columns['thread_title_edit_count'] = ['type' => self::UINT, 'forced' => true, 'default' => 0];

        return $structure;
    }

    /**
     * @param string|null $error
     * @return bool
     */
    public function canEditTitle(&$error = null)
    {
        if (is_callable([parent::class,'canEditTitle']))
        {
            /** @noinspection PhpUndefinedMethodInspection */
            return parent::canEditTitle($error);
        }

        return $this->canEdit($error);
    }
}
