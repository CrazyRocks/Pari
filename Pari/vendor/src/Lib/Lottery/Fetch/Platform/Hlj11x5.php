<?php
/**
 * 黑龙江11选5采集类
 */
namespace Lottery\Fetch\Platform;

Class Hlj11x5 extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 79;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'hlj11x5';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '09:05:00',
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

        //每5秒从caipiao.163.com尝试获取开奖数据，共尝试20次
        $caipiao163 = new \Lottery\Fetch\Source\Caipiao163('hljd11');
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $caipiao163->resentAwardNum($fetchExpect);
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

        //caipiao.163.com获取失败时每5秒从www.cailele.com尝试获取开奖数据，共尝试10次
        $lecai = new \Lottery\Fetch\Source\Cailele('hlj11x5');
        for ($i = 0; $i < 10; $i++) {
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
        $source = new \Lottery\Fetch\Source\Caipiao163('hlj11xuan5');
        $awardNumberList = $source->getAwardNumberListByDateAndHtml($date);
        $date_prev = substr($date, 0, 2);
        foreach ($awardNumberList as &$awardNumber) {
            $awardNumber['number'] = explode(' ', $awardNumber['number']);
            foreach ($awardNumber['number'] as &$v) {
                $v = ltrim($v, '0');
            }
            $awardNumber['number'] = implode(' ', $awardNumber['number']);
            $awardNumber['expect'] = $date_prev . $awardNumber['expect'];
        }
        return $awardNumberList;
    }


    public function formatExpect($expect, $opentime)
    {
        return date('Ymd', $opentime) . str_pad($expect, 2, '0', STR_PAD_LEFT);
    }
}
