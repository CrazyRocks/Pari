<?php
/**
 * 时时彩定单双判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Danshuang extends AbstractSsc
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

        $danCount = $shuangCount = 0;
        foreach ($this->awardNumbers as $awardNumber) {
            if ($awardNumber % 2 == 0) {
                $shuangCount++;
            } else {
                $danCount++;
            }
        }

        if ($betNumbers['a'] == '0单5双' && $danCount == 0 && $shuangCount == 5) {
            return 1;
        }

        if ($betNumbers['a'] == '5单0双' && $danCount == 5 && $shuangCount == 0) {
            return 2;
        }

        if ($betNumbers['a'] == '1单4双' && $danCount == 1 && $shuangCount == 4) {
            return 3;
        }

        if ($betNumbers['a'] == '4单1双' && $danCount == 4 && $shuangCount == 1) {
            return 4;
        }

        if ($betNumbers['a'] == '2单3双' && $danCount == 2 && $shuangCount == 3) {
            return 5;
        }

        if ($betNumbers['a'] == '3单2双' && $danCount == 3 && $shuangCount == 2) {
            return 6;
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
