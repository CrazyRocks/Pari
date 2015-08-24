<?php
/**
 * 快3任选判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class K3renxuan extends AbstractSsc
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
        if (empty($betNumbers) ||
            !is_array($betNumbers)
        ) {
            return 0;
        }

        //不定位 | 龙凤呈祥
        if ($star == 1 || $star == 2) {
            if (count($betNumbers) != count(array_unique($betNumbers))) {
                return 0;
            }
            if (count(array_intersect($betNumbers, $this->awardNumbers)) == min(count($betNumbers), count($this->awardNumbers))) {
                return 1;
            }
            return 0;
        //任选3 | 任选4
        } else {
            foreach ($this->awardNumbers as $awardNumber) {
                if (!in_array($awardNumber, $betNumbers)) {
                    return 0;
                }
            }
            return 1;
        }
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
