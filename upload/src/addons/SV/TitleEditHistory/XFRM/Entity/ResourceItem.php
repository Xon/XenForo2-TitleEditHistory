<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\TitleEditHistory\XFRM\Entity;

use SV\TitleEditHistory\Entity\IHistoryTrackedTitle;
use XF\Mvc\Entity\Structure;

/**
 * @extends \XFRM\Entity\ResourceItem
 *
 * @property int $resource_title_last_edit_date
 * @property int $resource_title_last_edit_user_id
 * @property int $resource_title_edit_count
 */
class ResourceItem extends XFCP_ResourceItem implements IHistoryTrackedTitle
{
    public function getTitleEditKeys(): array
    {
        return [
            'edit_date' => 'resource_date',
            'last_edit_date' => 'resource_title_last_edit_date',
            'last_edit_user_id' => 'resource_title_last_edit_user_id',
            'edit_count' => 'resource_title_edit_count',
            'content_type' => 'resource_title',
            'content_id' => 'resource_id',
            'title' => 'title',
            'editor' => \XFRM\Service\ResourceItem\Edit::class,
        ];
    }

    public function getTitleEditCount(): int
    {
        return $this->resource_title_edit_count;
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
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->columns['resource_title_last_edit_date'] = ['type' => self::UINT, 'default' => 0];
        $structure->columns['resource_title_last_edit_user_id'] = ['type' => self::UINT, 'default' => 0];
        $structure->columns['resource_title_edit_count'] = ['type' => self::UINT, 'forced' => true, 'default' => 0];

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