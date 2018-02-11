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

	protected function _isNotInsideSilentEditWindow() {
		return \XF::$time - $this->get('post_date') > \XF::options()->editLogDisplay['delay'] * 60;
	}

	protected function _preSave() {
		$upstream = parent::_preSave();

		if ($this->isUpdate() && $this->isChanged('title') && $this->_isNotInsideSilentEditWindow())
		{
			if (!$this->isChanged('thread_title_last_edit_date'))
			{
				$this->set('thread_title_last_edit_date', \XF::$time);

				if (!$this->isChanged('thread_title_last_edit_user_id'))
				{
					$this->set('thread_title_last_edit_user_id', \XF::visitor()->user_id);
				}
			}

			if (!$this->isChanged('thread_title_edit_count'))
			{
				$this->set('thread_title_edit_count', $this->get('thread_title_edit_count') + 1);
			}
		}
		if ($this->isChanged('thread_title_edit_count') && $this->get('thread_title_edit_count') == 0)
		{
			$this->set('thread_title_last_edit_date', 0);
		}
		if (!$this->get('thread_title_last_edit_date'))
		{
			$this->set('thread_title_last_edit_user_id', 0);
		}

		return $upstream;
	}

	protected function _postSave() {
		$upstream = parent::_postSave();

		if ($this->isUpdate() && $this->isChanged('title') && $this->_isNotInsideSilentEditWindow())
		{
			$this->_insertTitleEditHistory();
		}

		return $upstream;
	}

	protected function _insertTitleEditHistory()
	{
		$history = \XF::em()->create('XF:EditHistory');
		$history->bulkSet([
			'content_type' => 'thread_title',
			'content_id'   => $this->thread_id,
			'edit_user_id' => \XF::visitor()->user_id,
			'old_text'     => $this->getExistingValue('title')
		]);
		$history->save();
	}

	public static function getStructure(Structure $structure) {
		$structure = parent::getStructure($structure);

		$structure->columns['thread_title_last_edit_date'] = ['type' => self::UINT, 'default' => 0];
		$structure->columns['thread_title_last_edit_user_id'] = ['type' => self::UINT, 'default' => 0];
		$structure->columns['thread_title_edit_count'] = ['type' => self::UINT, 'forced' => true, 'default' => 0];

		return $structure;
	}

}
