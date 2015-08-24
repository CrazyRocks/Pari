<?php
/**
 * 河内（自定义越南）时时彩采集类
 */
namespace Lottery\Fetch\Platform;

class Hnwfc extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 288;

    public function getName()
    {
        return 'hnwfc';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '00:05:00',
                'end' => '23:55:00',
                'interval' => 300
            ),
            array(
                'start' => '00:00:00',
                'end' => '00:00:00',
                'interval' => 300
            )
        );
    }

    public function fetchAwardNumberInfo($expect)
    {
        $code_list = explode(',', '0,1,2,3,4,5,6,7,8,9');
        $number = [];
        for ($i = 0; $i < 5; $i++) {
            $number[] = $code_list[array_rand($code_list, 1)];
        }

	    return array(
            'number' => implode(' ', $number),
            'expect' => $expect,
	        'opendate' => strtotime(date('Y-m-d', $this->nowTimestamp)),
            'opentime' => 0
        );
    }

    public function getAwardNumberListByDate($date)
    {
        $awardNumberList = array();
        $opendate = strtotime($date);

        $start = strtotime($date . ' 00:05:00');
        $end = strtotime('+1 day', strtotime($date));
        for ($i = $start; $i <= $end; $i += 300) {
            $fetch = new self($i);
            $latestAwardExpectInfo = $fetch->getLatestAwardExpectInfo();
            if (!empty($latestAwardExpectInfo)) {
                $awardNumber = $fetch->fetchAwardNumberInfo($latestAwardExpectInfo['expect']);
                if ($awardNumber) {
                    $awardNumber['opendate'] = $opendate;
                    $awardNumber['opentime'] = $latestAwardExpectInfo['opentime'];
                    $awardNumberList[] = $awardNumber;
                }
            }
        }

        return $awardNumberList;
    }

    public function formatExpect($expect, $opentime)
    {
        $expectTimestamp = $expect == 288 ? strtotime('-1 day', $this->nowTimestamp) : $this->nowTimestamp;
        return date('Ymd', $expectTimestamp) . str_pad($expect, 3, '0', STR_PAD_LEFT);
    }
}
