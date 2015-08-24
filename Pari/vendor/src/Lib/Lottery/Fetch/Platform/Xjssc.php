<?php
/**
 * 新疆时时彩采集类
 */
namespace Lottery\Fetch\Platform;

Class Xjssc extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 96;

    const _168KAI_ID = 10022;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'xjssc';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '10:10:00',
                'end' => '23:50:00',
                'interval' => 600
            ),
            array(
                'start' => '00:00:00',
                'end' => '02:00:00',
                'interval' => 600
            )
        );
    }

    public function fetchAwardNumberInfo($expect)
    {
        //实际开奖时间延迟5秒左右
        sleep(5);

        //每5秒从apiplus尝试获取开奖数据，共尝试20次
        $apiplus = new \Lottery\Fetch\Source\Apiplus('xjssc');
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $apiplus->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', substr($expect, -2) >= 84 ? strtotime('-1 day', $this->nowTimestamp) : $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(10);
        }

        //每5秒从www.168kai.com尝试获取开奖数据，共尝试20次
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $source->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', substr($expect, -2) >= 84 ? strtotime('-1 day', $this->nowTimestamp) : $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(10);
        }

        //每5秒从官网尝试获取开奖数据，共尝试20次
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $this->fetchAwardNumberInfoByXjflcp($expect);
            if (!empty($awardNumberInfo)) {
                return $awardNumberInfo;
            }
            sleep(5);
        }

        return false;
    }

    protected function fetchAwardNumberInfoByXjflcp($expect)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://www.xjflcp.com/servlet/sscflash?type=full&rand=' . time(),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 15
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        if (!empty($content)) {
            $xml = @\simplexml_load_string($content);
            if (isset($xml->recent_draw->draw) &&
                $xml->recent_draw->draw == $expect &&
                isset($xml->recent_draw->prize_number) &&
                !empty($xml->recent_draw->prize_number)
            ) {
                return array(
                    'expect' => $expect,
                    'number' => str_replace('|', ' ', $xml->recent_draw->prize_number),
                    'opendate' => strtotime(date('Y-m-d', substr($expect, -2) >= 84 ? strtotime('-1 day', $this->nowTimestamp) : $this->nowTimestamp)),
                    'opentime' => 0
                );
            }
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
        $expectTimestamp = $expect >= 84 ? strtotime('-1 day', $opentime) : $opentime;
        return date('Ymd', $expectTimestamp) . str_pad($expect, 2, '0', STR_PAD_LEFT);
    }
}
