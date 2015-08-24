<?php
/**
 * 时时彩三星混合组选判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Threehunhezuxuan extends AbstractSsc
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

        if (count($betNumbers) != 3) {
            return 0;
        }

        if (!$this->checkBetNumbersEqAwardNumbersNoOrder($betNumbers)) {
            return 0;
        }

        if ($this->isZu3($betNumbers)) {
            return 1;
        }

        if ($this->isZu6($betNumbers)) {
            return 2;
        }

		return 0;
    }

    public function getBetCount($playway, $data)
    {
        return 0;
    }
}
