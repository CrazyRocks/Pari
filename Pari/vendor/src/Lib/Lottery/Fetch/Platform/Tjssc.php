<?php
/**
 * 天津时时彩采集类
 */
namespace Lottery\Fetch\Platform;

Class Tjssc extends AbstractSsc
{
    /**
     * 每日开奖期数
     *
     * @const int DAILY_OPEN_EXPECT_NUM
     */
    const DAILY_OPEN_EXPECT_NUM = 84;

    const _168KAI_ID = 10021;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'tjssc';
    }

    public function getAwardTimeInfo()
    {
        return array(
            array(
                'start' => '09:08:00',
                'end' => '22:58:00',
                'interval' => 600
            )
        );
    }

    public function fetchAwardNumberInfo($expect)
    {
        //实际开奖时间延迟60秒左右
        sleep(5);

        //每5秒从apiplus尝试获取开奖数据，共尝试20次
        $apiplus = new \Lottery\Fetch\Source\Apiplus('tjssc');
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $apiplus->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(10);
        }

        //每5秒从www.168kai.com尝试获取开奖数据，共尝试20次
        $source = new \Lottery\Fetch\Source\Caipiao168kai(self::_168KAI_ID);
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $source->getAwardNumberInfo($expect);
            if (!empty($awardNumberInfo)) {
                $awardNumberInfo['opendate'] = strtotime(date('Y-m-d', $this->nowTimestamp));
                return $awardNumberInfo;
            }
            sleep(5);
        }

        //每5秒从官网尝试获取开奖数据，共尝试20次
        for ($i = 0; $i < 20; $i++) {
            $awardNumberInfo = $this->fetchAwardNumberInfoByTjflcpw($expect);
            if (!empty($awardNumberInfo)) {
                return $awardNumberInfo;
            }
            sleep(5);
        }

        return false;
    }

    protected function fetchAwardNumberInfoByTjflcpw($expect)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://www.tjflcpw.com/xml/sscPrize.xml?' . time(),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 30
        ));
        $content = curl_exec($ch);
        curl_close($ch);

        if (!empty($content)) {
            $xml = @\simplexml_load_string($content);
            if (isset($xml->drawList)) {
                foreach ($xml->drawList as $v) {
                    if ($v->term == $expect) {
                        $code = strval($v->code);
                        $code_length = strlen($code);
                        $number = array();
                        for ($i = 0; $i < $code_length; $i++) {
                            if (($i + 1) %2 == 0) {
                                $number[] = $code[$i];
                            }
                        }
                        if ($number) {
                            return array(
                                'expect' => $expect,
                                'number' => implode(' ', $number),
                                'opendate' => strtotime(date('Y-m-d', $this->nowTimestamp)),
                                'opentime' => 0
                            );
                        }
                    }
                }
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
        return date('Ymd', $opentime) . str_pad($expect, 3, '0', STR_PAD_LEFT);
    }
}
