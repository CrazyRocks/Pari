<?php
/**
 * 时时彩包点判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Baodian extends AbstractSsc
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
            !is_array($betNumbers['pos']) ||
            !in_array(count($betNumbers['pos']), array(2, 3)) ||
            !isset($betNumbers['number'])
        ) {
            return 0;
        }
        $total = 0;
        $totalNumbers = array();
        foreach ($betNumbers['pos'] as $pos) {
            if (!isset($this->awardNumbers[$pos])) {
                return 0;
            }
            $total += $this->awardNumbers[$pos];
            $totalNumbers[] = $this->awardNumbers[$pos];
        }

        if ($total != $betNumbers['number']) {
            return 0;
        }

        if ($star == 2) {
            if ($this->isDuizi($totalNumbers)) {
                return 1;
            } else {
                return 2;
            }
        } elseif ($star == 3) {
            if ($this->isBaozi($totalNumbers)) {
                return 1;
            } elseif ($this->isZu3($totalNumbers)) {
                return 2;
            } else {
                return 3;
            }
        }

        return 0;
    }

    public function getBetCount($playway, $data)
    {
        $baodian_counts_2 = array('0' => 1, '1' => 1, '2' => 2, '3' => 2, '4' => 3, '5' => 3, '6' => 4, '7' => 4, '8' => 5, '9' => 5, '10' => 5, '11' => 4, '12' => 4, '13' => 3, '14' => 3, '15' => 2, '16' => 2, '17' => 1, '18' => 1);
        $baodian_counts_3 = array('0' => 1, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 7, '7' => 8, '8' => 10, '9' => 12, '10' => 13, '11' => 14, '12' => 15, '13' => 15, '14' => 15, '15' => 15, '16' => 14, '17' => 13, '18' => 12, '19' => 10, '20' => 8, '21' => 7, '22' => 5, '23' => 4, '24' => 3, '25' => 2, '26' => 1, '27' => 1);
        $betCount = 0;
        $playway_pos = explode(',', $playway['pos']);
        $playway_pos_count = count($playway_pos);

        if (isset($data['number']) && is_array($data['number'])) {
            foreach ($data['number'] as $v) {
                if ($playway_pos_count == 2) {
                    $betCount += isset($baodian_counts_2[$v]) ? $baodian_counts_2[$v] : 0;
                } elseif ($playway_pos_count == 3) {
                    $betCount += isset($baodian_counts_3[$v]) ? $baodian_counts_3[$v] : 0;
                }
            }
        }

        return $betCount;
    }
}
