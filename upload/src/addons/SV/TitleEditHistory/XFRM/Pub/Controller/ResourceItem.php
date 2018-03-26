<?php

namespace SV\TitleEditHistory\XFRM\Pub\Controller;

use XF\Mvc\ParameterBag;

class ResourceItem extends XFCP_ResourceItem
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
                'content_type' => 'resource_title',
                'content_id'   => $params->get('resource_id')
            ]
        );
    }
}