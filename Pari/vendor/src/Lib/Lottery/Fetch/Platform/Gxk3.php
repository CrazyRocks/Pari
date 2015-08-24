<?php
/**
 * 广西快3采集类
 */
namespace Lottery\Fetch\Platform;

Class Gxk3 extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 78;

    const _168KAI_ID = 10028;

    public function getName()
    {
        return 'gxk3';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '09:38:00',
                'end' => '22:28:00',
                'interval' => 600
            )
        );
    }

    public function fetchAwardNumberInfo($expect)
    {
        //实际开奖时间延迟60秒左右
        sleep(60);

        //每10秒从caipiao.163.com尝试获取开奖数据，共尝试30次
        $caipiao163 = new \Lottery\Fetch\Source\Caipiao163('gxkuai3');
        for ($i = 0; $i < 30; $i++) {
            $awardNumberInfo = $caipiao163->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(10);
        }

        //每5秒从apiplus尝试获取开奖数据，共尝试20次
        $apiplus = new \Lottery\Fetch\Source\Apiplus('gxk3');
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $apiplus->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(10);
        }

        $fetchExpect = substr($expect, 2);

        //每10从www.168kai.com尝试获取开奖数据，共尝试30次
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $source->getAwardNumberInfo($fetchExpect);
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
