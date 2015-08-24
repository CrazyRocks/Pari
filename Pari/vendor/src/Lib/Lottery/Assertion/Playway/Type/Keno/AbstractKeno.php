<?php
/**
 * 快乐彩判断是否中奖类
 */
namespace Lottery\Assertion\Playway\Type\Keno;

use Lottery\Assertion\Playway\Type\PlaywayTypeInterface;

abstract class AbstractKeno implements PlaywayTypeInterface
{
	/**
     * @param  array $awardNumbers 开奖号码
     */
	protected $awardNumbers;

	/**
     * @param  array $awardNumbersTotal 开奖号码总数
     */
	protected $awardNumbersTotal;

	/**
     * @param  array $jiOuCount 开奖号码奇偶总数
     */
	protected $jiOuCount = null;

	/**
     * construct
     *
     * @param  string|array $awardNumbers 开奖号码
     * @return Array
     */
	public function __construct($awardNumbers)
	{
		$this->setAwardNumbers($awardNumbers);
	}

	/**
     * 设置开奖号码
     *
	 * @param  string|array $awardNumbers 开奖号码
     * @return this
     */
	public function setAwardNumbers($awardNumbers)
	{
		if (is_string($awardNumbers)) {
			$awardNumbers = explode(' ', $awardNumbers);
		}

		$this->awardNumbers = $awardNumbers;
		$this->awardNumbersTotal = array_sum($awardNumbers);
		$this->jiOuCount = null;

		return $this;
	}

	/**
	 * 获取开奖号码奇偶总数
	 *
	 * @return boolean
	 */
	public function getJiOuCount()
	{
	    if ($this->jiOuCount === null) {
	        $this->jiOuCount = array(
	            'ji' => 0,
	            'ou' => 0
	        );
	        foreach ($this->getAwardNumbers() as $awardNumber) {
	            if ($awardNumber % 2 == 0) {
	                $this->jiOuCount['ou']++;
	            } else {
	                $this->jiOuCount['ji']++;
	            }
	        }
	    }

	    return $this->jiOuCount;
	}

	/**
     * 获取开奖号码总数
     *
     * @return Array
     */
	public function getAwardNumbersTotal()
	{
		return $this->awardNumbersTotal;
	}

	/**
     * 获取开奖号码
     *
     * @return Array
     */
	public function getAwardNumbers()
	{
		return $this->awardNumbers;
	}

	public function getBetCount($playway, $data)
	{
		return 1;
	}
}