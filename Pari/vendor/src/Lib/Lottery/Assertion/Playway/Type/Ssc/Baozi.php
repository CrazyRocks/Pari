<?php
/**
 * 时时彩豹子判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Baozi extends AbstractSsc
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
            count($betNumbers['pos']) != 3 ||
            !isset($betNumbers['number'])
        ) {
            return 0;
        }

        foreach ($betNumbers['pos'] as $pos) {
            if (!isset($this->awardNumbers[$pos])) {
                return 0;
            }
            if ($this->awardNumbers[$pos] != $betNumbers['number']) {
                return 0;
            }
        }

		return 1;
    }

    public function getBetCount($playway, $data)
    {
        return isset($data['number']) ? count($data['number']) : 0;
    }
}