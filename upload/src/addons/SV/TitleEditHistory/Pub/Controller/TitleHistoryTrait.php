<?php

namespace SV\TitleEditHistory\Pub\Controller;

use XF\Mvc\ParameterBag;

trait TitleHistoryTrait
{
    /** @noinspection PhpDocSignatureInspection */
    /**
     * @return array
     * @throws \LogicException
     */
    protected function getTitleHistoryKeys()
    {
        throw new \LogicException(get_called_class() . '::getTitleHistoryKeys() must be overridden');
    }

    /**
     * @param ParameterBag $params
     * @return \XF\Mvc\Reply\Reroute
     */
    public function actionTitleHistory(ParameterBag $params)
    {
        $keys = $this->getTitleHistoryKeys();
        return $this->rerouteController(
            'XF:EditHistory', 'index',
            [
                'content_type' => $keys['content_type'],
                'content_id'   => $params->get($keys['content_id'])
            ]
        );
    }
}