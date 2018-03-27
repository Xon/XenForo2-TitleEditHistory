<?php

namespace SV\TitleEditHistory\Pub\Controller;

use XF\Mvc\ParameterBag;

trait TitleHistoryTrait
{
    /**
     * @return string|null
     * @throws \LogicException
     */
    protected function getTitleHistoryContentType()
    {
        throw new \LogicException(get_called_class() . '::getTitleHistoryContentType() must be overridden');
    }

    /**
     * @return string|null
     * @throws \LogicException
     */
    protected function getTitleHistoryContentIdKey()
    {
        throw new \LogicException(get_called_class() . '::getTitleHistoryContentIdKey() must be overridden');
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Reroute
     */
    public function actionTitleHistory(ParameterBag $params)
    {
        return $this->rerouteController(
            'XF:EditHistory', 'index',
            [
                'content_type' => $this->getTitleHistoryContentType(),
                'content_id'   => $params->get($this->getTitleHistoryContentIdKey())
            ]
        );
    }
}