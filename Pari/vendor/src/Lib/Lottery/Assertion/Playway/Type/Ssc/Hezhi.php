<?php
/**
 * 时时彩和值判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Hezhi extends AbstractSsc
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
        if (!isset($betNumbers['pos']) ||
            empty($betNumbers['pos']) ||
            !is_array($betNumbers['pos']) ||
            !isset($betNumbers['number'])
        ) {
            return 0;
        }

        $total = 0;
        foreach ($betNumbers['pos'] as $pos) {
            if (!isset($this->awardNumbers[$pos])) {
                return 0;
            }
            $total += $this->awardNumbers[$pos];
        }

        if ($total != $betNumbers['number']) {
            return 0;
        }

        return 1;
    }

    public function getBetCount($playway, $data)
    {
        $hezhi_counts_2 = array('0' => 1, '1' => 2, '2' => 3, '3' => 4, '4' => 5, '5' => 6, '6' => 7, '7' => 8, '8' => 9, '9' => 10, '10' => 9, '11' => 8, '12' => 7, '13' => 6, '14' => 5, '15' => 4, '16' => 3, '17' => 2, '18' => 1);
        $hezhi_counts_3 = array('0' => 1, '1' => 3, '2' => 6, '3' => 10, '4' => 15, '5' => 21, '6' => 28, '7' => 36, '8' => 45, '9' => 55, '10' => 63, '11' => 69, '12' => 73, '13' => 75, '14' => 75, '15' => 73, '16' => 69, '17' => 63, '18' => 55, '19' => 45, '20' => 36, '21' => 28, '22' => 21, '23' => 15, '24' => 10, '25' => 6, '26' => 3, '27' => 1);
        $betCount = 0;
        $playway_pos = explode(',', $playway['pos']);
        $playway_pos_count = count($playway_pos);

        if (isset($data['number']) && is_array($data['number'])) {
            foreach ($data['number'] as $v) {
                if ($playway_pos_count == 2) {
                    $betCount += isset($hezhi_counts_2[$v]) ? $hezhi_counts_2[$v] : 0;
                } elseif ($playway_pos_count == 3) {
                    $betCount += isset($hezhi_counts_3[$v]) ? $hezhi_counts_3[$v] : 0;
                }
            }
        }

        return $betCount;
    }
}
