<?php
/**
 * 分分11选5(自定义11选5)采集类
 */
namespace Lottery\Fetch\Platform;

Class Ff11x5 extends Pt11x5
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 960;

    protected $fetchAwardNumberInfoDelay = 0;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'ff11x5';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '00:00:00',
                'end' => '23:58:30',
                'interval' => 90
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
        $currentSaleExpectInfo = $this->getCurrentSaleExpectInfo();
        if ($currentSaleExpectInfo['status'] != 1) {
            return false;
        }

        $deviation = $this->nowTimestamp - $currentSaleExpectInfo['opentime'];
        if ($deviation >= -70 && $deviation < 0) {
            $this->fetchAwardNumberInfoDelay = abs($deviation);
            return array(
                'expect' => $currentSaleExpectInfo['expect'],
                'opentime' => $currentSaleExpectInfo['opentime']
            );
        }

        return false;
    }

    public function fetchAwardNumberInfo($expect)
    {
        if ($this->fetchAwardNumberInfoDelay > 0) {
            sleep($this->fetchAwardNumberInfoDelay);
        }

        return parent::fetchAwardNumberInfo($expect);
    }

    public function getAwardNumberListByDate($date)
    {
        $awardNumberList = array();
        $opendate = strtotime($date);
        $now = time();

        foreach ($this->getAwardTimeInfo() as $item) {
            $start = strtotime($date . ' ' . $item['start']);
            $end = strtotime($date . ' ' . $item['end']);
            for ($i = $start; $i <= $end; $i += $item['interval']) {
                $fetch = new self($i - 1);
                $latestAwardExpectInfo = $fetch->getLatestAwardExpectInfo();
                if (!empty($latestAwardExpectInfo)) {
                    $awardNumber = $fetch->fetchAwardNumberInfo($latestAwardExpectInfo['expect']);
                    if ($awardNumber) {
                        if ($latestAwardExpectInfo['opentime'] < $now) {
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
}
