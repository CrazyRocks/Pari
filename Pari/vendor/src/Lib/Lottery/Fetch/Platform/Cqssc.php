<?php
/**
 * 重庆时时彩采集类
 */
namespace Lottery\Fetch\Platform;

Class Cqssc extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 120;

    const _168KAI_ID = 10011;

    public function getName()
    {
        return 'cqssc';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '00:05:00',
                'end' => '01:55:00',
                'interval' => 300
            ),
            array(
                'start' => '10:00:00',
                'end' => '22:00:00',
                'interval' => 600
            ),
            array(
                'start' => '22:05:00',
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
        //实际开奖时间延迟35秒左右
        sleep(35);
		$awardNumberInfo = array();
     	//每5秒从www.168kai.com尝试获取开奖数据，共尝试20次
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $source->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', substr($expect, -3) == 120 ? strtotime('-1 day', $this->nowTimestamp) : $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(5);
        }
        
        //每5秒从caipiao.163.com尝试获取开奖数据，共尝试30次
        $caipiao163 = new \Lottery\Fetch\Source\Caipiao163('ssc');
        $fetchExpect = substr($expect, 2);
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $caipiao163->getAwardNumberInfo($fetchExpect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['expect'] = $expect;
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', substr($expect, -3) == 120 ? strtotime('-1 day', $this->nowTimestamp) : $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(5);
        }

        //每5秒从apiplus尝试获取开奖数据，共尝试20次
        $apiplus = new \Lottery\Fetch\Source\Apiplus('cqssc');
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $apiplus->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', substr($expect, -3) == 120 ? strtotime('-1 day', $this->nowTimestamp) : $this->nowTimestamp));
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
        return $awardNumberList;
    }

    public function formatExpect($expect, $opentime)
    {
        $expectTimestamp = $expect == 120 ? strtotime('-1 day', $opentime) : $opentime;
        return date('Ymd', $expectTimestamp) . str_pad($expect, 3, '0', STR_PAD_LEFT);
    }
}
