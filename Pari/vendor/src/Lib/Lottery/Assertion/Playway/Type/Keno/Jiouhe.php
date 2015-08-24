<?php
/**
 * 快乐彩“奇偶和”判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Keno;

use Lottery\Assertion\Playway\Type\Keno\AbstractKeno;

class Jiouhe extends AbstractKeno
{
    /**
     * 是否中奖
     * @return int 中奖等级
     */
    public function assert($betNumbers = null, $star = null)
    {
        $jiOuCount = $this->getJiOuCount();
        return intval($jiOuCount['ji'] == 10 && $jiOuCount['ou'] == 10);
    }
}
