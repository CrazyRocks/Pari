<?php
/**
 * 时时彩二星组选(包1胆)判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class TwoBao1 extends AbstractSsc
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
        if (count($betNumbers) != 2) {
            return 0;
        }

        if (!$this->checkBetNumbersEqAwardNumbersNoOrder($betNumbers)) {
            return 0;
        }

        if ($this->isDuizi($betNumbers)) {
            return 1;
        }

        return 2;
    }

    public function getBetCount($playway, $data)
    {
        return isset($data['number']) ? (10 * count($data['number'])) : 0;
    }
}
