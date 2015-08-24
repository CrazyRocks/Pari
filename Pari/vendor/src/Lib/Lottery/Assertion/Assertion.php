<?php
/**
 * 判断是否中奖类
 */
namespace Lottery\Assertion;

class Assertion implements AssertionInterface
{
	/**
     * @param  array $awardNumbers 开奖号码
     */
	protected $awardNumbers;

	protected $assertionPlaywayTypes;

	/**
     * construct
     *
     * @param  string|array $lotteryNumbers 开奖号码
     * @return Array
     */
	public function __construct($awardNumbers = array())
	{
		$this->awardNumbers = $awardNumbers;
	}

	public function getAssertionPlaywayType($playway_type)
	{
		if (!isset($this->assertionPlaywayTypes[$playway_type])) {
			if ($playway_type == 'ssc-3bao1') {
				$assertionPlaywayTypeClassName = "\\Lottery\\Assertion\\Playway\\Type\\Ssc\\ThreeBao1";
			} elseif ($playway_type == 'ssc-3bao2') {
				$assertionPlaywayTypeClassName = "\\Lottery\\Assertion\\Playway\\Type\\Ssc\\ThreeBao2";
			} elseif ($playway_type == 'ssc-2bao1') {
				$assertionPlaywayTypeClassName = "\\Lottery\\Assertion\\Playway\\Type\\Ssc\\TwoBao1";
			} elseif ($playway_type == 'ssc-3zu') {
				$assertionPlaywayTypeClassName = "\\Lottery\\Assertion\\Playway\\Type\\Ssc\\ThreeZu";
			} elseif ($playway_type == 'ssc-2zhi1') {
				$assertionPlaywayTypeClassName = "\\Lottery\\Assertion\\Playway\\Type\\Ssc\\TwoZhi1";
			} elseif ($playway_type == 'ssc-3zhi1') {
				$assertionPlaywayTypeClassName = "\\Lottery\\Assertion\\Playway\\Type\\Ssc\\ThreeZhi1";
			} else {
				$playway_type_arr = array_map('ucfirst', explode('-', $playway_type));
				$assertionPlaywayTypeClassName = "\\Lottery\\Assertion\\Playway\\Type\\" . implode('\\', $playway_type_arr);
			}

			if (!class_exists($assertionPlaywayTypeClassName)) {
				$this->assertionPlaywayTypes[$playway_type] = false;
			}

			$this->assertionPlaywayTypes[$playway_type] = new $assertionPlaywayTypeClassName($this->awardNumbers);
		}

		return $this->assertionPlaywayTypes[$playway_type];
	}

	/**
     * 判断是否中奖
     *
     * @param string $playway_type 玩法类型名
     * @param array $betNumbers 投注号码
     * @param int $star 玩法星级
     * @return int
     */
    public function assert($playway_type, $betNumbers = null, $star = null)
	{
        $assertionPlaywayType = $this->getAssertionPlaywayType($playway_type);
		if (empty($assertionPlaywayType)) {
			return 0;
		}

        return $assertionPlaywayType->assert($betNumbers, $star);
	}
}