<?php
/**
 * 时时彩红黑胆判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Hongheidan extends AbstractSsc
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
            !isset($betNumbers['number2'])
        ) {
            return 0;
        }

        $isHong = false;
        $isHei = true;
        foreach ($betNumbers['pos'] as $pos) {
            if (!isset($this->awardNumbers[$pos])) {
                return 0;
            }
            if ($this->awardNumbers[$pos] == $betNumbers['number']) {
                $isHong = true;
            } elseif ($this->awardNumbers[$pos] == $betNumbers['number2']) {
                $isHei = false;
            }
        }

        if (!$isHong || !$isHei) {
            return 0;
        }

		return 1;
    }

    public function getBetCount($playway, $data)
    {
        if (!isset($data['number']) ||
            !is_array($data['number']) ||
            !isset($data['number2']) ||
            !is_array($data['number2'])
        ) {
            return 0;
        }

        return count($data['number']) * count($data['number2']);
    }
}
