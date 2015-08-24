<?php
/**
 * 时时彩五星组120判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Wxzu120 extends AbstractSsc
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
        if (count($betNumbers) != $star) {
            return 0;
        }

        if (!$this->checkBetNumbersEqAwardNumbersNoOrder($betNumbers)) {
            return 0;
        }

        return 1;
    }

    public function getBetCount($playway, $data)
    {
        if (!isset($data['number'])) {
            return 0;
        }

        $pailie_count = function ($m, $n) {
            $t = $m;
            for ($i = 1; $i < $n; $i++) {
                $t = $t * ($m - $i);
            }
            return $t;
        };
        $i = count($data['number']);
        $betCount = $pailie_count($i, $playway['min_bet_num']) / $pailie_count($playway['min_bet_num'], $playway['min_bet_num']);

        return $betCount;
    }
}
