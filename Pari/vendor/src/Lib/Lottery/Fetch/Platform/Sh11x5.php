<?php
/**
 * 上海11选5采集类
 */
namespace Lottery\Fetch\Platform;

Class Sh11x5 extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 90;

    const _168KAI_ID = 10024;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'sh11x5';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '09:00:00',
                'end' => '23:50:00',
                'interval' => 600
            )
        );
    }

    public function fetchAwardNumberInfo($expect)
    {
        //实际开奖时间延迟60秒左右
        sleep(60);

        //每5秒从www.168kai.com尝试获取开奖数据，共尝试20次
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $source->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(10);
        }

        //每5秒从www.cailele.com尝试获取开奖数据，共尝试40次
        $lecai = new \Lottery\Fetch\Source\Cailele('sh11x5');
        for ($i = 0; $i < 40; $i++) {
            $awardNumberInfo = $lecai->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['expect'] = $expect;
                $awardNumberInfo['number'] = explode(' ', $awardNumberInfo['number']);
                foreach ($awardNumberInfo['number'] as &$v) {
                    $v = ltrim($v, '0');
                }
                $awardNumberInfo['number'] = implode(' ', $awardNumberInfo['number']);
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
        return $awardNumberList;
    }

    public function formatExpect($expect, $opentime)
    {
        return date('Ymd', $opentime) . str_pad($expect, 2, '0', STR_PAD_LEFT);
    }
}
