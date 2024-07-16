<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\TitleEditHistory\XFMG\Entity;

use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use XF\Mvc\Entity\Structure;

/**
 * @extends \XFMG\Entity\MediaItem
 *
 * COLUMNS
 * @property int $media_title_last_edit_date
 * @property int $media_title_last_edit_user_id
 * @property int $media_title_edit_count
 */
class MediaItem extends XFCP_MediaItem implements IHistoryTrackedTitle
{
    public function getTitleEditKeys(): array
    {
        return [
            'edit_date' => 'media_date',
            'last_edit_date' => 'media_title_last_edit_date',
            'last_edit_user_id' => 'media_title_last_edit_user_id',
            'edit_count' => 'media_title_edit_count',
            'content_type' => 'xfmg_media_title',
            'content_id' => 'media_id',
            'title' => 'title',
            'editor' => \XFMG\Service\Media\Editor::class,
        ];
    }

    public function getTitleEditCount(): int
    {
        return $this->media_title_edit_count;
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

    public function canViewTitleHistory(&$error = null): bool
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

        return $this->hasPermission('editAny');
    }

    /**
     * @param Structure $structure
     *
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->columns['media_title_last_edit_date'] = ['type' => self::UINT, 'default' => 0];
        $structure->columns['media_title_last_edit_user_id'] = ['type' => self::UINT, 'default' => 0];
        $structure->columns['media_title_edit_count'] = ['type' => self::UINT, 'forced' => true, 'default' => 0];

        return $structure;
    }
}