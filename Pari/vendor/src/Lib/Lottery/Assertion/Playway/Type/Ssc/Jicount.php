<?php
/**
 * 时时彩猜奇次判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Jicount extends AbstractSsc
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

        $jiCount = 0;
        foreach ($betNumbers['pos'] as $pos) {
            if (!isset($this->awardNumbers[$pos])) {
                return 0;
            }
            if ($this->awardNumbers[$pos] % 2 != 0) {
                $jiCount++;
            }
        }

        if ($jiCount != $betNumbers['number']) {
            return 0;
        }

		return 1;
    }

    public function getBetCount($playway, $data)
    {
        if (!isset($data['number']) || !is_array($data['number'])) {
            return 0;
        }

        $jiouCounts = array(0 => 1, 1 => 3, 2 => 3, 3 => 1);
        $betCount = 0;
        foreach ($data['number'] as $v) {
            $betCount += isset($jiouCounts[$v]) ? $jiouCounts[$v] : 0;
        }

        return $betCount;
    }
}
