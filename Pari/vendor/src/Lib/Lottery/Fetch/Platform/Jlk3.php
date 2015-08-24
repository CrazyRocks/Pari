<?php
/**
 * 吉林快3采集类
 */
namespace Lottery\Fetch\Platform;

Class Jlk3 extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 79;

    const _168KAI_ID = 10013;

    public function getName()
    {
        return 'jlk3';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '08:30:00',
                'end' => '21:30:00',
                'interval' => 600
            )
        );
    }

    public function fetchAwardNumberInfo($expect)
    {
        //实际开奖时间延迟60秒左右
        sleep(60);

        $fetchExpect = substr($expect, 2);

        //每10秒从caipiao.163.com尝试获取开奖数据，共尝试10次
        $caipiao163 = new \Lottery\Fetch\Source\Caipiao163('kuai3');
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $caipiao163->getAwardNumberInfo($fetchExpect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                $awardNumberInfo['expect'] = $expect;
                return $awardNumberInfo;
            }
            sleep(10);
        }

        //每5秒从apiplus尝试获取开奖数据，共尝试20次
        $apiplus = new \Lottery\Fetch\Source\Apiplus('jlk3');
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $apiplus->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(10);
        }

        //每10从www.cailele.com尝试获取开奖数据，共尝试10次
        $lecai = new \Lottery\Fetch\Source\Cailele('jlk3');
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $lecai->getAwardNumberInfo($fetchExpect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                $awardNumberInfo['expect'] = $expect;
                return $awardNumberInfo;
            }
            sleep(10);
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
        return date('Ymd', $opentime) . str_pad($expect, 3, '0', STR_PAD_LEFT);
    }
}
