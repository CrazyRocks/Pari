<?php
/**
 * 快3和值判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class K3hezhi extends AbstractSsc
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

        if (in_array($betNumbers['number'], array(3, 18))) {
            return 1;
        }

        if (in_array($betNumbers['number'], array(4, 17))) {
            return 2;
        }

        if (in_array($betNumbers['number'], array(5, 16))) {
            return 3;
        }

        if (in_array($betNumbers['number'], array(6, 15))) {
            return 4;
        }

        if (in_array($betNumbers['number'], array(7, 14))) {
            return 5;
        }

        if (in_array($betNumbers['number'], array(8, 13))) {
            return 6;
        }

        if (in_array($betNumbers['number'], array(9, 12))) {
            return 7;
        }

        if (in_array($betNumbers['number'], array(10, 11))) {
            return 8;
        }

		return 0;
    }

    public function getBetCount($playway, $data)
    {
        if (!isset($data['number']) || !is_array($data['number'])) {
            return 0;
        }

        return count($data['number']);
    }
}
