<?php

namespace SV\TitleEditHistory\XFRM\Entity;

use XF\Mvc\Entity\Structure;

class ResourceItem extends XFCP_ResourceItem
{
    /**
     * @param string|null $error
     * @return bool
     */
    public function canViewResourceTitleHistory(&$error = null)
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

        $structure->columns['resource_title_last_edit_date'] = ['type' => self::UINT, 'default' => 0];
        $structure->columns['resource_title_last_edit_user_id'] = ['type' => self::UINT, 'default' => 0];
        $structure->columns['resource_title_edit_count'] = ['type' => self::UINT, 'forced' => true, 'default' => 0];

        return $structure;
    }
}