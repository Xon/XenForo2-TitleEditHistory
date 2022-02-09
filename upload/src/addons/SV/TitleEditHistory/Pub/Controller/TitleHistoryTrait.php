<?php
/** @noinspection PhpMissingReturnTypeInspection */

namespace SV\TitleEditHistory\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;

trait TitleHistoryTrait
{
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
     * @return AbstractReply
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