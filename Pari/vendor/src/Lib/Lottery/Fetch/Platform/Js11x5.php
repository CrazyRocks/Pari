<?php
/**
 * 江苏11选5采集类
 */
namespace Lottery\Fetch\Platform;

Class Js11x5 extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 82;

    const _168KAI_ID = 10044;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'js11x5';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '08:35:00',
                'end' => '22:05:00',
                'interval' => 600
            )
        );
    }

    public function fetchAwardNumberInfo($expect)
    {
        //实际开奖时间延迟60秒左右
        sleep(60);

        $fetchExpect = substr($expect, 2);

        //每5秒从www.168kai.com尝试获取开奖数据，共尝试40次
        $lecai = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        for ($i = 0; $i < 40; $i++) {
            $awardNumberInfo = $lecai->getAwardNumberInfo($fetchExpect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['expect'] = $expect;
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(5);
        }

        return false;
    }

    public function getAwardNumberListByDate($date)
    {
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        $awardNumberList = $source->getAwardNumberListByDate($date);
        $date_prev = substr($date, 0, 2);
        foreach ($awardNumberList as &$awardNumber) {
            $awardNumber['expect'] = $date_prev . $awardNumber['expect'];
        }
        return $awardNumberList;
    }

    public function formatExpect($expect, $opentime)
    {
        return date('Ymd', $opentime) . str_pad($expect, 2, '0', STR_PAD_LEFT);
    }
}
