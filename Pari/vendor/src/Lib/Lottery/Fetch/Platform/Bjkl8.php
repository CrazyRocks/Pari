<?php
/**
 * 北京快乐8采集类
 */
namespace Lottery\Fetch\Platform;

Class Bjkl8 extends AbstractKeno
{
    const _168KAI_ID = 10014;

    /**
     * 开始统计日期
     *
     * @const string START_COUNT_DATE
     */
    const START_COUNT_DATE = '2015-01-01';

    /**
     * 开始统计期号
     *
     * @const int START_COUNT_EXPECT
     */
    const START_COUNT_EXPECT = 672248;

    /**
     * 每日开奖期数
     *
     * @const int OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 179;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'bjkl8';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '09:05:00',
                'end' => '23:55:00',
                'interval' => 300
            )
        );
    }

    /**
     * 返回开盘情况
     *
     * @return Array
     */
    public function getOpeningInfo()
    {
        $openingInfo = array('isOpening' => true, 'estimateOpentime' => 0);
        $now_day = date('Y-m-d', $this->nowTimestamp);

        if (($estimateOpenCountdown = strtotime($now_day . ' 09:00:00') - $this->nowTimestamp) > 0) {
            $openingInfo = array(
                'isOpening' => false,
                'estimateOpentime' => $now_day . ' 09:00:00',
                'estimateOpenCountdown' => $estimateOpenCountdown
            );
        } else if ($this->nowTimestamp >= strtotime($now_day . ' 23:55:00')) {
            $tomorrow_opentime = strtotime('+1 day', strtotime($now_day)) + 9*3600;
            $openingInfo = array(
                'isOpening' => false,
                'estimateOpentime' => date('Y-m-d H:i:s', $tomorrow_opentime),
                'estimateOpenCountdown' => $tomorrow_opentime - $this->nowTimestamp
            );
        }

        return $openingInfo;
    }

    public function getLatestAwardExpectInfo()
    {
		//获取开始统计日期到现在时间的天数
		$startCountDatetime = new \DateTime(self::START_COUNT_DATE);
		$nowDatetime = new \DateTime(date('Y-m-d', $this->nowTimestamp));
		$datetimeDiff = $startCountDatetime->diff($nowDatetime);
		$diffDay = intval($datetimeDiff->format('%a'));
		//今天第一期期号
		$expect = self::START_COUNT_EXPECT + ($diffDay * self::DAILY_OPEN_EXPECT_NUM);
		$opentime = 0;
		foreach ($this->getAwardTimeInfo() as $item) {
		    $start = strtotime($item['start'], $this->nowTimestamp);
		    $end = strtotime($item['end'], $this->nowTimestamp);
			for ($i = $start; $i <= $end; $i += $item['interval']) {
			    $deviation = $this->nowTimestamp - $i;
			    $minDeviation = isset($item['minDeviation']) ? $item['minDeviation'] : 0;
			    $maxDeviation = isset($item['maxDeviation']) ? $item['maxDeviation'] : 30;
	            if ($deviation >= $minDeviation && $deviation <= $maxDeviation) {
			        $opentime = $i;
			        break 2;
			    }
			    $expect++;
			}
		}

		if (!$opentime) {
		    return false;
		}

		//休市判断
		$restDay = 0;
		foreach (\Lottery\Fetch\Rest::getRestTimeInfo($this->getName()) as $item) {
		    if ($opentime >= strtotime($item['start']) &&
		        $opentime < strtotime($item['end'])
		    ) {
		        return false;
		    } elseif ($opentime >= strtotime($item['end'])) {
		        $startDatetime = new \DateTime($item['start']);
		        $endDatetime = new \DateTime($item['end']);
		        $datetimeDiff = $startDatetime->diff($endDatetime);
		        $restDay += intval($datetimeDiff->format('%a'));
		    }
		}
		$expect -= $restDay * self::DAILY_OPEN_EXPECT_NUM;

		return array(
		    'expect' => $this->formatExpect($expect, $opentime),
		    'opentime' => $opentime
		);
    }

    public function fetchAwardNumberInfo($expect)
    {
        //实际开奖时间延迟2秒左右
        sleep(2);

        $awardNumberInfo = false;
        //每2秒从caipiao.163.com尝试获取开奖数据，共尝试10次
        $caipiao163 = new \Lottery\Fetch\Source\Caipiao163('kl8');
        for ($i = 0; $i < 10; $i++) {
            $awardNumberInfo = $caipiao163->resentAwardNum($expect);
            if (!empty($awardNumberInfo)) {
                $numbers = explode(':', $awardNumberInfo['number']);
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                $awardNumberInfo['number'] = $numbers[0];
                return $awardNumberInfo;
            }
            sleep(2);
        }

        //每10从www.168kai.com尝试获取开奖数据，共尝试20次
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $source->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                $awardNumberInfo['number'] = explode(' ', $awardNumberInfo['number']);
                unset($awardNumberInfo['number'][20]);
                $awardNumberInfo['number'] = implode(' ', $awardNumberInfo['number']);
                return $awardNumberInfo;
            }
            sleep(10);
        }

        return false;
    }

    public function getCurrentSaleExpectInfo($deadline = 30)
    {
        //获取开始统计日期到现在时间的天数
		$startCountDatetime = new \DateTime(self::START_COUNT_DATE);
		$nowDatetime = new \DateTime(date('Y-m-d', $this->nowTimestamp));
		$datetimeDiff = $startCountDatetime->diff($nowDatetime);
		$diffDay = intval($datetimeDiff->format('%a'));
		//今天第一期期号
		$expect = self::START_COUNT_EXPECT + ($diffDay * self::DAILY_OPEN_EXPECT_NUM) - 1;
		$opentime = 0;
		$awardTimeInfo = $this->getAwardTimeInfo();
		$restTimeInfo = \Lottery\Fetch\Rest::getRestTimeInfo($this->getName());
		$isRest = false;
		foreach ($restTimeInfo as $item) {
		    if ($this->nowTimestamp >= strtotime($item['start']) &&
		        $this->nowTimestamp < strtotime($item['end'])
		    ) {
		        $isRest = true;
		        break;
		    }
		}
		$countTimestamp = $this->nowTimestamp + $deadline;
		foreach ($awardTimeInfo as $item) {
		    $start = strtotime($item['start'], $this->nowTimestamp);
		    $end = strtotime($item['end'], $this->nowTimestamp);
			for ($i = $start; $i <= $end; $i += $item['interval']) {
			    if (!$isRest) {
			        $expect++;
			    }
			    if ($countTimestamp < $i) {
			        $opentime = $i;
			        break 2;
			    }
			}
		}

		if (!$opentime) {
		    //当日最后一期后当前销售期号为次日第一期
		    $expect++;
		    $opentime = strtotime(date('Y-m-d', strtotime('+1 day', $this->nowTimestamp)) . ' ' . $awardTimeInfo[0]['start']);
		}

		//休市判断
		$restDay = 0;
		foreach ($restTimeInfo as $item) {
		    if ($opentime >= strtotime($item['start']) &&
		        $opentime < strtotime($item['end'])
		    ) {
		        //在休市时间内，当前销售期号为休市结束后第一期
		        $opentime = strtotime(date('Y-m-d', strtotime($item['end'])) . ' ' . $awardTimeInfo[0]['start']);
		    } elseif ($opentime >= strtotime($item['end'])) {
		        $startDatetime = new \DateTime($item['start']);
		        $endDatetime = new \DateTime($item['end']);
		        $datetimeDiff = $startDatetime->diff($endDatetime);
		        $restDay += intval($datetimeDiff->format('%a'));
		    }
		}
		$expect -= $restDay * self::DAILY_OPEN_EXPECT_NUM;

		return array(
		    'status' => 1,
		    'expect' => $this->formatExpect($expect, $opentime),
		    'opentime' => $opentime
		);
    }

    public function getAwardNumberListByDate($date)
    {
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        $awardNumberList = $source->getAwardNumberListByDate($date);
        $date_prev = substr($date, 0, 2);
        foreach ($awardNumberList as &$awardNumber) {
            $awardNumber['number'] = explode(' ', $awardNumber['number']);
            unset($awardNumber['number'][20]);
            $awardNumber['number'] = implode(' ', $awardNumber['number']);
        }
        return $awardNumberList;
    }

    public function formatExpect($expect, $opentime)
    {
        return $expect;
    }
}
