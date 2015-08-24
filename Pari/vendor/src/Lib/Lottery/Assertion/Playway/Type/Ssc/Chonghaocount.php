<?php
/**
 * 时时彩猜重号数判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Chonghaocount extends AbstractSsc
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
            !isset($betNumbers['number']) ||
            !isset($betNumbers['number2']) ||
            !in_array($betNumbers['number2'], array(1, 2, 3, 4))
        ) {
            return 0;
        }

        $chonghaocount = 0;
        foreach ($betNumbers['pos'] as $pos) {
            if (!isset($this->awardNumbers[$pos])) {
                return 0;
            }
            if ($this->awardNumbers[$pos] == $betNumbers['number']) {
                $chonghaocount++;
            }
        }

        if ($chonghaocount < $betNumbers['number2']) {
            return 0;
        }

		return 1;
    }

    public function getBetCount($playway, $data)
    {
        return isset($data['number']) ? count($data['number']) : 0;
    }
}
