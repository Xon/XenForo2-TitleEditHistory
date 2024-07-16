<?php

namespace SV\TitleEditHistory\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;

trait TitleHistoryTrait
{
    protected function getTitleHistoryKeys(): array
    {
        throw new \LogicException(get_called_class() . '::getTitleHistoryKeys() must be overridden');
    }

    public function actionTitleHistory(ParameterBag $params): AbstractReply
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