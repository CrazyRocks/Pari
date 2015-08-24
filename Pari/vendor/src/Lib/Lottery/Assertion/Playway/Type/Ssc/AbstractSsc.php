<?php
/**
 * 时时彩玩法类型判断中奖抽象类
 */
namespace Lottery\Assertion\Playway\Type\Ssc;

use Lottery\Assertion\Playway\Type\PlaywayTypeInterface;

abstract class AbstractSsc implements PlaywayTypeInterface
{

    /**
     *
     * @var array $lotteryNumbers 开奖号码
     */
    protected $awardNumbers;

    /**
     * construct
     *
     * @param string|array $awardNumbers
     *            开奖号码
     * @return Array
     */
    public function __construct($awardNumbers)
    {
        $this->setAwardNumbers($awardNumbers);
    }

    /**
     * 设置开奖号码
     *
     * @param string|array $lotteryNumbers
     *            开奖号码
     * @return this
     */
    public function setAwardNumbers($awardNumbers)
    {
        if (is_string($awardNumbers)) {
            $awardNumbers = preg_split('/\s|,/', $awardNumbers);
        }

        $awardNumbers = array_reverse($awardNumbers);
        $str = 'abcdefghijklmnopqrstuvwxyz';
        $newAwardNumbers = [];
        foreach ($awardNumbers as $k => $value) {
            if (isset($str[$k])) {
                $newAwardNumbers[$str[$k]] = $value;
            }
        }

        $this->awardNumbers = $newAwardNumbers;

        return $this;
    }

    /**
     * 检查投注数组和开奖数组相等情况，用法类似substr
     *
     * @param array $betNumbers
     *            投注号码
     * @return bool
     */
    public function checkBetNumbersEqAwardNumbers(array $betNumbers, $start = 0, $length = null)
    {
        $start = (int) $start;
        if ($start < 0) {
            $start = abs($start) - 1;
            $betNumbers = array_reverse($betNumbers);
        }

        if ($length === 0) {
            return false;
        }

        $isEq = true;
        $i = 0;

        foreach ($betNumbers as $k => $v) {
            if ($i < $start) {
                continue;
            }
            if ($i === $length) {
                break;
            }
            $i ++;

            if (! isset($this->awardNumbers[$k]) || $v != $this->awardNumbers[$k]) {
                $isEq = false;
                break;
            }
        }

        return $isEq;
    }

    /**
     * 检测投注位置上的数字是否在开奖结果中这些位置上都有(顺序无关)
     *
     * @param array $betNumbers
     *            投注号码
     * @return bool
     */
    public function checkBetNumbersEqAwardNumbersNoOrder(array $betNumbers)
    {
        $newAwardNumbers = array();
        foreach ($betNumbers as $k => $v) {
            if (isset($this->awardNumbers[$k])) {
                $newAwardNumbers[$k] = $this->awardNumbers[$k];
            }
        }

        sort($betNumbers);
        sort($newAwardNumbers);

        return $betNumbers == $newAwardNumbers;
    }

    /**
     * 检测是否是组三形态
     *
     * @param
     *            $number
     *
     * @return bool
     */
    final public function isZu3($number)
    {
        if (count($number) == 3 && count(array_unique($number)) == 2) {
            return true;
        }
        return false;
    }

    /**
     * 检测是否是组6形态
     *
     * @param
     *            $number
     *
     * @return bool
     */
    final public function isZu6($number)
    {
        if (count($number) == 3 && count(array_unique($number)) == 3) {
            return true;
        }
        return false;
    }

    /**
     * 检测是否是豹子形态
     *
     * @param
     *            $number
     *
     * @return bool
     */
    final public function isBaozi($number)
    {
        if (count($number) == 3 && count(array_unique($number)) == 1) {
            return true;
        }
        return false;
    }

    /**
     * 检测是否是对子形态
     *
     * @param
     *            $number
     *
     * @return bool
     */
    final public function isDuizi($number)
    {
        if (count($number) == 2 && count(array_unique($number)) == 1) {
            return true;
        }
        return false;
    }

    /**
     * 过滤掉 空字符串,false,null,空数组,保留 0 '0'
     *
     * @param
     *            $arr
     *
     * @return array
     */
    final public function filterEmpty(array $arr)
    {
        return array_filter($arr, function ($v)
        {
            if ($v === '' || $v === false || $v === [] || $v === null) {
                return false;
            }
            return true;
        });
    }
}
