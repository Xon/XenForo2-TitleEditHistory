<?php

namespace SV\TitleEditHistory\XF\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\View;

class Thread extends XFCP_Thread
{

	public function actionTitleHistory(ParameterBag $params)
	{
		return $this->rerouteController('XF:EditHistory', 'index', array(
			'content_type' => 'thread_title',
			'content_id' => $params->get('thread_id')
		));
	}


}