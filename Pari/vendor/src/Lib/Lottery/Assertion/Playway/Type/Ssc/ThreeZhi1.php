<?php
/**
 * 时时彩三星直选(包1胆)判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class ThreeZhi1 extends AbstractSsc
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
        if (count($betNumbers) != 3) {
            return 0;
        }

        $filterBetNumbers = $this->filterEmpty($betNumbers);
        if (count($filterBetNumbers) != 1) {
            return 0;
        }
        $firstBetNumber = current($filterBetNumbers);

        foreach ($betNumbers as $k => $v) {
            if (isset($this->awardNumbers[$k]) && $firstBetNumber == $this->awardNumbers[$k]) {
                return 1;
            }
        }

        return 0;
    }

    public function getBetCount($playway, $data)
    {
        return isset($data['number']) ? count($data['number']) : 0;
    }
}
