<?php
/**
 * 时时彩连选判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Lian extends AbstractSsc
{
    /**
     * 是否中奖
     *
     * @param string|array $betNumbers
     *            投注号码
     * @param int $star
     *            星级
     * @return int 中奖等级
     */
    public function assert($betNumbers = null, $star = null)
    {
        $betNumbers = $this->filterEmpty($betNumbers);

        if (!$this->checkBetNumbersEqAwardNumbers($betNumbers)) {
            return 0;
        }

        $betNumbersCount = count($betNumbers);
        if ($star == 5) {
            if ($betNumbersCount == 5) {
                return 1;
            } elseif ($betNumbersCount == 3) {
                return 2;
            } elseif ($betNumbersCount == 2) {
                return 3;
            } elseif ($betNumbersCount == 1) {
                return 4;
            }
        } elseif ($star == 4) {
            if ($betNumbersCount == 4) {
                return 1;
            } elseif ($betNumbersCount == 2) {
                return 2;
            } elseif ($betNumbersCount == 1) {
                return 3;
            }
        } elseif ($star == 3) {
            if ($betNumbersCount == 3) {
                return 1;
            } elseif ($betNumbersCount == 2) {
                return 2;
            } elseif ($betNumbersCount == 1) {
                return 3;
            }
        } elseif ($star == 2) {
            if ($betNumbersCount == 2) {
                return 1;
            } elseif ($betNumbersCount == 1) {
                return 2;
            }
        }

        return 0;
    }

    public function getBetCount($playway, $data)
    {
        $star = count(explode(',', $playway['pos']));
        if (count($data) != $star) {
            return 0;
        }

        $betCount = 0;
        $i = 1;
        ksort($data);
        foreach ($data as $v) {
            $i = $i * count($v);
        }
        $betCount += $i;
        if ($star == 5) {
            $temp = array_slice($data, 0, 3, true);
            $i = 1;
            foreach ($temp as $v) {
                $i = $i * count($v);
            }
            $betCount += $i;
            $temp = array_slice($data, 0, 2, true);
            $i = 1;
            foreach ($temp as $v) {
                $i = $i * count($v);
            }
            $betCount += $i;
            $temp = array_slice($data, 0, 1, true);
            $i = 1;
            foreach ($temp as $v) {
                $i = $i * count($v);
            }
            $betCount += $i;
        }
        if ($star == 3 || $star == 4) {
            if (key($data) == 'b' && $star == 3) {
                //中三
                $temp1 = array_slice($data, 0, 1, true);
                $temp2 = array_slice($data, 2, 1, true);
                $temp = array_merge($temp1, $temp2);
                $i = 1;
                foreach ($temp as $v) {
                    $i = $i * count($v);
                }
                $betCount += $i;
                $temp = array_slice($data, 1, 1, true);
                $i = 1;
                foreach ($temp as $v) {
                    $i = $i * count($v);
                }
                $betCount += $i;
            } else {
                if (key($data) == 'a') {
                    ksort($data);
                    //后三后四
                } else {
                    //前三前四
                    krsort($data);
                }
                $temp = array_slice($data, 0, 2, true);
                $i = 1;
                foreach ($temp as $v) {
                    $i = $i * count($v);
                }
                $betCount += $i;
                $temp = array_slice($data, 0, 1, true);
                $i = 1;
                foreach ($temp as $v) {
                    $i = $i * count($v);
                }
                $betCount += $i;
            }
        }
        if ($star == 2) {
            $temp = array_slice($data, 0, 1, true);
            $i = 1;
            foreach ($temp as $v) {
                $i = $i * count($v);
            }
            $betCount += $i;
        }

        return $betCount;
    }
}
