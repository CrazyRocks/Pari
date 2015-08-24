<?php
/**
 * 玩法类型判断中奖类接口
 */
namespace Lottery\Assertion\Playway\Type;

interface PlaywayTypeInterface
{
    /**
     * 设置开奖号码
     *
     * @param  string|array $lotteryNumbers 开奖号码
     * @return this
     */
    public function setAwardNumbers($awardNumbers);

    /**
     * 是否中奖
     *
     * @param string|array $betNumbers 投注号码
     * @param int $star 星级
     * @return int 中奖等级
     */
    public function assert($betNumbers = null, $star = null);
}
