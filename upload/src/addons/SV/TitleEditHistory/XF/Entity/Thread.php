<?php

namespace SV\TitleEditHistory\XF\Entity;

use XF\Mvc\Entity\Structure;

/**
 *
 * @property int thread_title_last_edit_date
 * @property int thread_title_last_edit_user_id
 * @property int thread_title_edit_count
 */
class Thread extends XFCP_Thread {

	public function canViewThreadTitleHistory(&$error = null) {
		if (! \XF::visitor()->user_id)
		{
			return false;
		}

		if (! \XF::options()->editHistory['enabled'])
		{
			return false;
		}

		if (\XF::visitor()->hasNodePermission($this->node_id, 'manageAnyThread'))
		{
			return true;
		}

		return false;
	}

	public static function getStructure(Structure $structure) {
		$structure = parent::getStructure($structure);

		$structure->columns['thread_title_last_edit_date'] = ['type' => self::UINT, 'default' => 0];
		$structure->columns['thread_title_last_edit_user_id'] = ['type' => self::UINT, 'default' => 0];
		$structure->columns['thread_title_edit_count'] = ['type' => self::UINT, 'forced' => true, 'default' => 0];

		return $structure;
	}

}
