<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\TitleEditHistory\XFMG\Entity;

use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use XF\Mvc\Entity\Structure;

/**
 * Class Album
 *
 * @package SV\TitleEditHistory\XFMG\Entity
 *
 * COLUMNS
 * @property int album_title_last_edit_date
 * @property int album_title_last_edit_user_id
 * @property int album_title_edit_count
 */
class Album extends XFCP_Album implements IHistoryTrackedTitle
{
    /**
     * @return array
     */
    public function getTitleEditKeys()
    {
        return [
            'edit_date' => 'create_date',
            'last_edit_date' => 'album_title_last_edit_date',
            'last_edit_user_id' => 'album_title_last_edit_user_id',
            'edit_count' => 'album_title_edit_count',
            'content_type' => 'xfmg_album_title',
            'content_id' => 'album_id',
            'title' => 'title',
            'editor' => 'XFMG:Album\Editor',
        ];
    }

    /**
     * @return int
     */
    public function getTitleEditCount()
    {
        return $this->album_title_edit_count;
    }

    /**
     * @param null $error
     *
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

    /**
     * @param null $error
     *
     * @return bool
     */
    public function canViewTitleHistory(&$error = null)
    {
        $visitor = \XF::visitor();

        if (!$visitor->user_id)
        {
            return false;
        }

        if (!$this->app()->options()->editHistory['enabled'])
        {
            return false;
        }

        return $this->hasPermission('editAnyAlbum');
    }

    /**
     * @param Structure $structure
     *
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->columns['album_title_last_edit_date'] = ['type' => self::UINT, 'default' => 0];
        $structure->columns['album_title_last_edit_user_id'] = ['type' => self::UINT, 'default' => 0];
        $structure->columns['album_title_edit_count'] = ['type' => self::UINT, 'forced' => true, 'default' => 0];

        return $structure;
    }
}