<?php
/**
 * 传统彩采集抽象类
 */
namespace Lottery\Fetch\Platform;

abstract class AbstractSsc implements PlatformInterface
{
    /**
     * 当前时间戳
     *
     * @var string $nowTimestamp
     */
    protected $nowTimestamp;

    public function __construct($nowTimestamp = null)
    {
        $this->nowTimestamp = $nowTimestamp ?: time();
    }

    /**
     * 获取最新采集期号信息
     *
     * @return array(
                   'expect' => '141106036',
                   'opentime' => 1402143035
               )
     */
    public function getLatestAwardExpectInfo()
    {
		$expect = $opentime = 0;
		foreach ($this->getAwardTimeInfo() as $item) {
		    $start = strtotime($item['start'], $this->nowTimestamp);
		    $end = strtotime($item['end'], $this->nowTimestamp);
			for ($i = $start; $i <= $end; $i += $item['interval']) {
			    $expect++;
			    $deviation = $this->nowTimestamp - $i;
			    $minDeviation = isset($item['minDeviation']) ? $item['minDeviation'] : 0;
			    $maxDeviation = isset($item['maxDeviation']) ? $item['maxDeviation'] : 30;
	            if ($deviation >= $minDeviation && $deviation <= $maxDeviation) {
			        $opentime = $i;
			        break 2;
			    }
			}
		}

		if (!$opentime) {
		    return false;
		}

		//休市判断
		foreach (\Lottery\Fetch\Rest::getRestTimeInfo($this->getName()) as $item) {
		    if ($opentime >= strtotime($item['start']) &&
		        $opentime <= strtotime($item['end'])
		    ) {
		        return false;
		    }
		}

		return array(
		    'expect' => $this->formatExpect($expect, $opentime),
		    'opentime' => $opentime
		);
    }

    /**
     * 获取当前销售期号信息
     * @param int $deadline 截止时间
     *
     * @return array(
                   'status' => 1, //1 成功 0 失败
                   'expect' => '141106036',
                   'opentime' => 1402143035
               )
     */
    public function getCurrentSaleExpectInfo($deadline = 30)
    {
        $countTimestamp = $this->nowTimestamp;
        if ($this->getName() == 'xjssc') {
            $nowTimeHis = date('H:i:s', $this->nowTimestamp);
            if ($nowTimeHis >= '00:00:00' && $nowTimeHis < '02:00:00') {
                $countTimestamp += 3600 * 24;
            }
        }
        $countTimestamp += $deadline;
		$expect = $opentime = $sales_second = 0;
		$awardTimeInfo = $this->getAwardTimeInfo();
		foreach ($awardTimeInfo as $item) {
		    $start = strtotime($item['start'], $this->nowTimestamp);
		    $end = strtotime($item['end'], $this->nowTimestamp);
		    if ($item['start'] == '00:00:00') {
		        $start += 3600 * 24;
		        $end += 3600 * 24;
		    }
			for ($i = $start; $i <= $end; $i += $item['interval']) {
			    $expect++;
			    if ($countTimestamp < $i) {
			        $opentime = $i;
					$sales_second = $item['interval'];
			        break 2;
			    }
			}
		}

		if (!$opentime) {
		    if (!isset($awardTimeInfo[0]['start'])) {
		        return array('status' => 0);
		    }
		    //当日最后一期后当前销售期号为次日第一期
		    $expect = 1;
		    $opentime = strtotime(date('Y-m-d', strtotime('+1 day', $this->nowTimestamp)) . ' ' . $awardTimeInfo[0]['start']);
			$sales_second = $awardTimeInfo[0]['interval'];
		}

		//上一期信息
		$prev_expect = $expect - 1;
		if ($prev_expect == 0) {
			$prev_expect = static::DAILY_OPEN_EXPECT_NUM;
		}
		$prev_expect = $this->formatExpect($prev_expect, $opentime - $sales_second);

		//休市判断
		if ($this->getName() == 'xjssc' && $expect >= 84) {
		    $opentime -= 3600 * 24;
		}
		foreach (\Lottery\Fetch\Rest::getRestTimeInfo($this->getName()) as $item) {
		    if ($opentime >= strtotime($item['start']) &&
		        $opentime < strtotime($item['end'])
		    ) {
		        if (!isset($awardTimeInfo[0]['start'])) {
		            return array('status' => 0);
		        }
                //在休市时间内，当前销售期号为休市结束后第一期
		        $expect = 1;
		        $opentime = strtotime(date('Y-m-d', strtotime($item['end'])) . ' ' . $awardTimeInfo[0]['start']);
				$sales_second = $awardTimeInfo[0]['interval'];
				$prev_expect = static::DAILY_OPEN_EXPECT_NUM;
				$prev_expect = $this->formatExpect($prev_expect, strtotime($item['start']) - 1);
		    }
		}

		return array(
		    'status' => 1,
		    'expect' => $this->formatExpect($expect, $opentime),
			'prev_expect' => $prev_expect,
			'sales_second' => $sales_second,
		    'opentime' => $opentime
		);
    }

	/**
	 * 获取余下即将销售期号
	 * @param int $deadline
	 * @param int $expect_num
     */
	public function getSaleExpectList($deadline = 30, $expect_num = 120)
	{
		$expect_list = array();
		$fetch_time = time();

		for ($i = 0; $i < $expect_num; $i++) {
			$fetch_platform = new static($fetch_time);
			$nextExpectInfo = $fetch_platform->getCurrentSaleExpectInfo($deadline);
			$expect_list[] = $nextExpectInfo;
			$fetch_time = $nextExpectInfo['opentime'] + 1;
		}

		return $expect_list;
	}
}
