<?php
/**
 * 自定义快乐彩采集类1
 */
namespace Lottery\Fetch\Platform;

Class Customkeno2 extends AbstractCustomKeno
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 480;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'customkeno2';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '00:00:00',
                'end' => '23:55:00',
                'interval' => 300
            )
        );
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
        //获取开始统计日期到现在时间的天数
		$startCountDatetime = new \DateTime($this->getStartCountDate());
		$nowDatetime = new \DateTime(date('Y-m-d', $this->nowTimestamp));
		$datetimeDiff = $startCountDatetime->diff($nowDatetime);
		$diffDay = intval($datetimeDiff->format('%a'));
		//今天第一期期号
		$expect = $this->getStartCountExpect() + ($diffDay * $this->getDailyOpenExpectNum());
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

		return array(
		    'expect' => $this->formatExpect($expect, $opentime),
		    'opentime' => $opentime
		);
    }

    public function getAwardNumberListByDate($date)
    {
        if ($date < $this->getStartCountDate()) {
            return array();
        }

        $awardNumberList = array();
        $opendate = strtotime($date);
        $now = time();

        foreach ($this->getAwardTimeInfo() as $item) {
            $start = strtotime($date . ' ' . $item['start']);
            $end = strtotime($date . ' ' . $item['end']);
            for ($i = $start; $i <= $end; $i += $item['interval']) {
                $fetch = new self($i);
                $latestAwardExpectInfo = $fetch->getLatestAwardExpectInfo();
                if (!empty($latestAwardExpectInfo)) {
                    $awardNumber = $fetch->fetchAwardNumberInfo($latestAwardExpectInfo['expect']);
                    if ($awardNumber) {
                        if ($latestAwardExpectInfo['opentime'] <= $now) {
                            $awardNumber['opendate'] = $opendate;
                            $awardNumber['opentime'] = $latestAwardExpectInfo['opentime'];
                            $awardNumberList[] = $awardNumber;
                        }
                    }
                }
            }
        }

        return $awardNumberList;
    }

    /**
     * 获取开始统计时间
     */
    public function getStartCountDate()
    {
        return '2014-10-23';
    }

    /**
     * 获取开始统计期号
     */
    public function getStartCountExpect()
    {
        return 1;
    }

    /**
     * 获取每日开奖期数
     */
    public function getDailyOpenExpectNum()
    {
        return 288;
    }
}
