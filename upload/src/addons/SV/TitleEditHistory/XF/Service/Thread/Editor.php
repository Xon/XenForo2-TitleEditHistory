<?php

namespace SV\TitleEditHistory\XF\Service\Thread;


/**
 * Extends \XF\Service\Thread\Editor
 */
class Editor extends XFCP_Editor
{

	protected $logEdit = true;
	protected $oldTitle;
	protected $logDelay;
	protected $logHistory = true;


	public function logEdit($logEdit)
	{
		$this->logEdit = $logEdit;
	}

	public function logDelay($logDelay)
	{
		$this->logDelay = $logDelay;
	}

	public function logHistory($logHistory)
	{
		$this->logHistory = $logHistory;
	}

	public function setTitle($title) {
		$oldTitle = $this->thread->title;

		$upstream = parent::setTitle($title);

		if($this->thread->isChanged('title')){
			$this->setupEditHistory($oldTitle);
		}

	    return $upstream;
	}

	protected function setupEditHistory($oldTitle)
	{
		$thread = $this->thread;

		$options = $this->app->options();
		if ($options->editLogDisplay['enabled'] && $this->logEdit)
		{
			$delay = is_null($this->logDelay) ? $options->editLogDisplay['delay'] * 60 : $this->logDelay;
			if ($thread->post_date + $delay <= \XF::$time)
			{
				$thread->thread_title_edit_count++;
				$thread->thread_title_last_edit_date = \XF::$time;
				$thread->thread_title_last_edit_user_id = \XF::visitor()->user_id;
			}
		}

		if ($options->editHistory['enabled'] && $this->logHistory)
		{
			$this->oldTitle = $oldTitle;
		}
	}


    /**
     * @return \XF\Entity\Thread
     */
    protected function _save()
    {
	    $visitor = \XF::visitor();

	    $db = $this->db();
	    $db->beginTransaction();

        $thread = parent::_save();

	    if ($this->oldTitle)
	    {
		    /** @var \XF\Repository\EditHistory $repo */
		    $repo = $this->repository('XF:EditHistory');
		    $repo->insertEditHistory('thread_title', $thread, $visitor, $this->oldTitle, $this->app->request()->getIp());
	    }

	    $db->commit();

	    return $thread;
    }

}
