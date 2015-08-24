<?php
/**
 * 采集彩种类接口
 */
namespace Lottery\Assertion;

interface AssertionInterface
{
    /**
     * 判断是否中奖
     *
     * @param string $playway_type 玩法类型名
     * @param array $betNumbers 投注号码
     * @param int $star 玩法星级
     * @return int
     */
    public function assert($playway_type, $betNumbers = null, $star = null);
}
