<?php
/**
 * 时时彩定位胆判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Ding extends AbstractSsc
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

        if (count($betNumbers) != 1) {
            return 0;
        }

        if ($this->checkBetNumbersEqAwardNumbers($betNumbers)) {
            return 1;
        }

        return 0;
    }

    public function getBetCount($playway, $data)
    {
        $betCount = 0;
        foreach ($data as $v) {
            if (!is_array($v)) {
                return 0;
            }
            $betCount += count($v);
        }

        return $betCount;
    }
}
