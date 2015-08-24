<?php
/**
 * 快乐彩“大”判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Keno;

use Lottery\Assertion\Playway\Type\Keno\AbstractKeno;

class Da extends AbstractKeno
{
    /**
     * 是否中奖
     * @return int 中奖等级
     */
    public function assert($betNumbers = null, $star = null)
    {
        return intval($this->getAwardNumbersTotal() > 810);
    }
}
