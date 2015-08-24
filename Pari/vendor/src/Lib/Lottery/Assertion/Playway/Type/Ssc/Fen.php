<?php
/**
 * 时时彩分位判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Fen extends AbstractSsc
{
    /**
     * 是否中奖
     *
     * @param string|array $betNumbers
     *            投注号码
     * @return int 中奖等级
     */
    public function assert($betNumbers = null, $star = null)
    {
        $betNumbers = $this->filterEmpty($betNumbers);

        if (!$this->checkBetNumbersEqAwardNumbersNoOrder($betNumbers)) {
            return 0;
        }

        if (count(array_unique($betNumbers)) == 1) {
            return 1;
        }

        return 2;
    }

    public function getBetCount($playway, $data)
    {
        return count(current($data)) * count(next($data));
    }
}
