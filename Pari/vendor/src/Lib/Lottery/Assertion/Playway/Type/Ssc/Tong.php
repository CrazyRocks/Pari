<?php
/**
 * 时时彩通选判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\Ssc\AbstractSsc;

class Tong extends AbstractSsc
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

        if (count($betNumbers) != $star) {
            return 0;
        }

        //检查一等奖
		if ($this->checkBetNumbersEqAwardNumbers($betNumbers)) {
		    return 1;
		}
		//检查二等奖
		$secondPrizecheckLengths = array(5 => 3, 4 => 3, 3 => 2);
		if (isset($secondPrizecheckLengths[$star]) &&
		    ($this->checkBetNumbersEqAwardNumbers($betNumbers, 0, $secondPrizecheckLengths[$star]) ||
		     $this->checkBetNumbersEqAwardNumbers($betNumbers, -1, $secondPrizecheckLengths[$star])
		    )
		) {
		    return 2;
		}
		//检查三等奖
		$threePrizecheckLengths = array(5 => 2, 4 => 2);
		if (isset($threePrizecheckLengths[$star]) &&
		    ($this->checkBetNumbersEqAwardNumbers($betNumbers, 0, $threePrizecheckLengths[$star]) ||
		     $this->checkBetNumbersEqAwardNumbers($betNumbers, -1, $threePrizecheckLengths[$star])
		    )
		) {
		    return 3;
		}

		return 0;
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
