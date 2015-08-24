<?php
/**
 * 时时彩猜中位判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Zhongwei extends AbstractSsc
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
        if (!isset($betNumbers['a'])) {
            return 0;
        }

        $awardNumbers = array_values($this->awardNumbers);
        sort($awardNumbers);
        $zhongweiKey = floor(count($awardNumbers) / 2);

        if (!isset($awardNumbers[$zhongweiKey]) || $betNumbers['a'] != $awardNumbers[$zhongweiKey]) {
            return 0;
        }

        if ($awardNumbers[$zhongweiKey] == 3 || $awardNumbers[$zhongweiKey] == 9) {
            return 1;
        }

        if ($awardNumbers[$zhongweiKey] == 4 || $awardNumbers[$zhongweiKey] == 8) {
            return 2;
        }

        if ($awardNumbers[$zhongweiKey] == 5 || $awardNumbers[$zhongweiKey] == 7) {
            return 3;
        }

        if ($awardNumbers[$zhongweiKey] == 6) {
            return 4;
        }

        return 0;
    }

    public function getBetCount($playway, $data)
    {
        if (!isset($data['a']) || !is_array($data['a'])) {
            return 0;
        }

        $pailie_count = function ($m, $n) {
            $t = $m;
            for ($i = 1; $i < $n; $i++) {
                $t = $t * ($m - $i);
            }
            return $t;
        };
        $i = count($data['a']);

        return $pailie_count($i, $playway['min_bet_num']) / $pailie_count($playway['min_bet_num'], $playway['min_bet_num']);
    }
}
