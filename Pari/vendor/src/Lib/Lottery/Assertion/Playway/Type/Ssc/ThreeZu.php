<?php
/**
 * 时时彩三星组选(包胆、玩法兼容包1胆和包2胆)判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class ThreeZu extends AbstractSsc
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
        $betNumbers = $this->filterEmpty($betNumbers);

        if (count($betNumbers) != 3) {
            return 0;
        }

        if (!$this->checkBetNumbersEqAwardNumbersNoOrder($betNumbers)) {
            return 0;
        }

        if ($this->isBaozi($betNumbers)) {
            return 0;
        }

        if ($this->isZu3($betNumbers)) {
            return 1;
        }

        if ($this->isZu6($betNumbers)) {
            return 2;
        }

		return 0;
    }

    public function getBetCount($playway, $data)
    {
        $betCount = 0;

        if (isset($data['row_setting']) && $data['row_setting'] == 'two') {
            $pailie_count = function ($m, $n) {
                $t = $m;
                for ($i = 1; $i < $n; $i++) {
                    $t = $t * ($m - $i);
                }
                return $t;
            };
            $i = 0;
            foreach ($data['number'] as $v) {
                $i++;
            }
            $betCount = $pailie_count($i, 2) / $pailie_count(2, 2) * 10;
        } else {
            $betCount = 55 * count($data['number']);
        }

        return $betCount;
    }
}
