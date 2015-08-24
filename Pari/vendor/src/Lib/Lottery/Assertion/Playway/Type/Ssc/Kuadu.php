<?php
/**
 * 时时彩跨度判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Kuadu extends AbstractSsc
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
            !in_array(count($betNumbers['pos']), array(2)) ||
            !isset($betNumbers['number'])
        ) {
            return 0;
        }
        $total = 0;
        foreach ($betNumbers['pos'] as $pos) {
            if (!isset($this->awardNumbers[$pos])) {
                return 0;
            }
            $total = abs($this->awardNumbers[$pos] - $total);
        }

        if ($total == $betNumbers['number']) {
            return 1;
        }

        return 0;
    }

    public function getBetCount($playway, $data)
    {
        if (!isset($data['number']) || !is_array($data['number'])) {
            return 0;
        }

        $kuadu_counts = array('0' => 10, '1' => 18, '2' => 16, '3' => 14, '4' => 12, '5' => 10, '6' => 8, '7' => 6, '8' => 4, '9' => 2);
        $betCount = 0;
        foreach ($data['number'] as $v) {
            $betCount += isset($kuadu_counts[$v]) ? $kuadu_counts[$v] : 0;
        }

        return $betCount;
    }
}
