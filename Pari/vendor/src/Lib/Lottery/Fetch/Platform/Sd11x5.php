<?php
/**
 * 山东11选5采集类
 */
namespace Lottery\Fetch\Platform;

Class Sd11x5 extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 78;

    protected $caipiao163GameEn = 'd11';

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'sd11x5';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '09:05:00',
                'end' => '21:55:00',
                'interval' => 600
            )
        );
    }

    public function fetchAwardNumberInfo($expect)
    {
        //实际开奖时间延迟60秒左右
        sleep(60);

        $awardNumberInfo = false;
        //每5秒从caipiao.163.com尝试获取开奖数据，共尝试30次
        $caipiao163 = new \Lottery\Fetch\Source\Caipiao163($this->caipiao163GameEn);
        for ($i = 0; $i < 30; $i++) {
            $awardNumberInfo = $caipiao163->getAwardNumberInfo(substr($expect, 2));
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
        $source = new \Lottery\Fetch\Source\Caipiao163('11xuan5');
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
