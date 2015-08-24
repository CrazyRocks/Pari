<?php
/**
 * 时时彩大小单双判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Daxiaodanshuang extends AbstractSsc
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
        if (!isset($betNumbers) ||
            empty($betNumbers) ||
            !is_array($betNumbers)
        ) {
            return 0;
        }

        $winLevel = 1;
        foreach ($betNumbers as $pos => $betNumber) {
            if (!isset($this->awardNumbers[$pos])) {
                $winLevel = 0;
            }

            if ($betNumber == '大') {
                if ($this->awardNumbers[$pos] < 5 ||
                    $this->awardNumbers[$pos] > 9
                ) {
                    $winLevel = 0;
                }
            } elseif ($betNumber == '小') {
                if ($this->awardNumbers[$pos] < 0 ||
                    $this->awardNumbers[$pos] > 4
                ) {
                    $winLevel = 0;
                }
            } elseif ($betNumber == '单') {
                if ($this->awardNumbers[$pos] % 2 == 0) {
                    $winLevel = 0;
                }
            } elseif ($betNumber == '双') {
                if ($this->awardNumbers[$pos] % 2 != 0) {
                    $winLevel = 0;
                }
            } else {
                $winLevel = 0;
            }
        }

        return $winLevel;
    }

    public function getBetCount($playway, $data)
    {
        $betCount = 1;
        foreach ($data as $v) {
            if (!is_array($v)) {
                return 0;
            }
            $betCount *= count($v);
        }

        return $betCount;
    }
}
