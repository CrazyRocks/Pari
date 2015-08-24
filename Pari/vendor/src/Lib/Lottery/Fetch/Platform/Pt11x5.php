<?php
/**
 * PT11选5(自定义11选5)采集类
 */
namespace Lottery\Fetch\Platform;

Class Pt11x5 extends AbstractCustomSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 144;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'pt11x5';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '00:00:00',
                'end' => '23:50:00',
                'interval' => 600
            )
        );
    }

    public function fetchAwardNumberInfo($expect)
    {
        $code_list = explode(',', '1,2,3,4,5,6,7,8,9,10,11');
        shuffle($code_list);
        return array(
            'number' => implode(' ', array_slice($code_list, 0, 5)),
            'expect' => $expect,
            'opendate' => strtotime(date('Y-m-d', $this->nowTimestamp)),
            'opentime' => 0
        );
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
                $fetch = new self($i);
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

    public function formatExpect($expect, $opentime)
    {
        return date('Ymd', $opentime) . str_pad($expect, 3, '0', STR_PAD_LEFT);
    }
}
