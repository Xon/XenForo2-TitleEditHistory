<?php

namespace SV\TitleEditHistory\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Thread extends XFCP_Thread
{
    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Reroute
     */
    public function actionTitleHistory(ParameterBag $params)
    {
        return $this->rerouteController(
            'XF:EditHistory', 'index',
            [
                'content_type' => 'thread_title',
                'content_id'   => $params->get('thread_id')
            ]
        );
    }
}
