<?php
/**
 * 时时彩三星直选(包2胆)判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Threezhi2 extends AbstractSsc
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
        if (!isset($betNumbers['number']) ||
            !is_array($betNumbers['number']) ||
            empty($betNumbers['number']) ||
            !isset($betNumbers['pos']) ||
            !is_array($betNumbers['pos']) ||
            empty($betNumbers['pos'])
        ) {
            return 0;
        }

        $checkNumbers = array();
        foreach ($betNumbers['pos'] as $pos) {
            if (!isset($this->awardNumbers[$pos])) {
                return 0;
            }
            $checkNumbers[] = $this->awardNumbers[$pos];
        }



        foreach ($betNumbers['number'] as $number) {
            if (!in_array($number, $checkNumbers)) {
                return 0;
            }
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
